<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\ApiTestCase;

class ContractorTest extends ApiTestCase
{
    use DatabaseTransactions;

    public function testSearchContractorsWithSkills()
    {
        $this->actingAs($this->user, 'api');

        /** @var User $contractor1 */
        $contractor1 = factory(User::class)->create();
        $skills1 = 'Appliance installation,Drywall/Plaster';
        $contractor1->saveSkills($skills1);

        /** @var User $contractor2 */
        $contractor2 = factory(User::class)->create();
        $skills2 = 'Drywall/Plaster,Framing';
        $contractor2->saveSkills($skills2);

        /** @var User $contractor3 */
        $contractor3 = factory(User::class)->create();
        $skills3 = 'Masonry,Painting,Siding';
        $contractor3->saveSkills($skills3);

        $response = $this->getJson(
            '/api/v1/contractor?' . http_build_query([
                'skills' => 'Appliance installation'
            ])
        )
            ->assertOk()
            ->assertJsonCount(1, 'data');
        $this->assertEquals($contractor1->id, $response->json('data.0.id'));

        $response = $this->getJson(
            '/api/v1/contractor?' . http_build_query([
                'skills' => $skills1
            ])
        )
            ->assertOk()
            ->assertJsonCount(2, 'data');
        $response_ids = array_pluck($response->json('data'), 'id');
        $this->assertInArray($contractor1->id, $response_ids);
        $this->assertInArray($contractor2->id, $response_ids);

        $response = $this->getJson(
            '/api/v1/contractor?' . http_build_query([
                'skills' => $skills3
            ])
        )
            ->assertOk()
            ->assertJsonCount(1, 'data');
        $this->assertEquals($contractor3->id, $response->json('data.0.id'));
    }

    public function testSearchContractorsPaginates()
    {
        $this->actingAs($this->user, 'api');

        /** @var Collection|User[] $contractors */
        $contractors = factory(User::class, 10)->create();
        $skills = 'Appliance installation,Drywall/Plaster';
        foreach ($contractors as $contractor) {
            $contractor->saveSkills($skills);
        }

        $this->assertApiPaginatesData('/api/v1/contractor?' . http_build_query(['skills' => $skills]), 10, 3);
    }

    public function testSearchContractorsWithLocation()
    {
        $this->actingAs($this->user, 'api');

        $skills = 'Appliance installation,Drywall/Plaster';

        /** @var User $contractor1 */
        $contractor1 = factory(User::class)->create([
            'city' => 'Alachua',
            'state' => 'FL',
        ]);
        $contractor1->saveSkills($skills);

        /** @var User $contractor2 */
        $contractor2 = factory(User::class)->create([
            'city' => 'Alachua',
            'state' => 'FL',
        ]);
        $contractor2->saveSkills($skills);

        /** @var User $contractor3 */
        $contractor3 = factory(User::class)->create([
            'city' => 'Bartow',
            'state' => 'FL',
        ]);
        $contractor3->saveSkills($skills);

        $response = $this->getJson(
            '/api/v1/contractor?' . http_build_query([
                'city' => 'Alachua',
                'state' => 'FL',
            ])
        )
            ->assertOk()
            ->assertJsonCount(2, 'data');
        $response_ids = array_pluck($response->json('data'), 'id');
        $this->assertInArray($contractor1->id, $response_ids);
        $this->assertInArray($contractor2->id, $response_ids);

        $response = $this->getJson(
            '/api/v1/contractor?' . http_build_query([
                'city' => 'Bartow',
                'state' => 'FL',
            ])
        )
            ->assertOk()
            ->assertJsonCount(1, 'data');
        $this->assertEquals($contractor3->id, $response->json('data.0.id'));
    }

    public function testGetContractorsNearbyLocation()
    {
        $this->actingAs($this->user, 'api');

        $this->user->update([
            'street_address' => '2202 E 20th Ave',
            'city' => 'Tampa',
            'state' => 'FL',
            'postal_code' => '33605',
        ]);
        $this->user->location
            ->update([
                'lat' => 27.969610,
                'lng' => -82.434497,
            ]);

        $skills = 'Appliance installation,Drywall/Plaster';

        /** @var User $contractor1 */
        $contractor1 = factory(User::class)->create([
            'street_address' => '1917 E 21st Ave',
            'city' => 'Tampa',
            'state' => 'FL',
            'postal_code' => '33605',
        ]);
        $contractor1->saveSkills($skills);
        $contractor1->location
            ->update([
                'lat' => 27.970256,
                'lng' => -82.436936,
            ]);

        /** @var User $contractor2 */
        $contractor2 = factory(User::class)->create([
            'street_address' => '1902 E 21st Ave',
            'city' => 'Tampa',
            'state' => 'FL',
            'postal_code' => '33605',
        ]);
        $contractor2->saveSkills($skills);
        $contractor2->location
            ->update([
                'lat' => 27.970679,
                'lng' => -82.437657,
            ]);

        /** @var User $contractor3 */
        $contractor3 = factory(User::class)->create([
            'street_address' => '4512 Ethan Way',
            'city' => 'Plant City',
            'state' => 'FL',
            'postal_code' => '33563',
        ]);
        $contractor3->saveSkills($skills);
        $contractor3->location
            ->update([
                'lat' => 28.013731,
                'lng' => -82.169591,
            ]);
        $response = $this->getJson(
            '/api/v1/contractor?' . http_build_query([
                'nearby' => [
                    'radius' => 15,
                ]
            ])
        )
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $response_ids = array_pluck($response->json('data'), 'id');
        /** @var User $user */
        foreach ([$contractor1, $contractor2] as $user) {
            $this->assertInArray($user->id, $response_ids);
        }
    }

    public function testGetContractorsNearbyCoordinates()
    {
        $this->actingAs($this->user, 'api');
        $skills = 'Appliance installation,Drywall/Plaster';

        /** @var User $contractor1 */
        $contractor1 = factory(User::class)->create([
            'street_address' => '1917 E 21st Ave',
            'city' => 'Tampa',
            'state' => 'FL',
            'postal_code' => '33605',
        ]);
        $contractor1->saveSkills($skills);
        $contractor1->location
            ->update([
                'lat' => 27.970256,
                'lng' => -82.436936,
            ]);

        /** @var User $contractor2 */
        $contractor2 = factory(User::class)->create([
            'street_address' => '1902 E 21st Ave',
            'city' => 'Tampa',
            'state' => 'FL',
            'postal_code' => '33605',
        ]);
        $contractor2->saveSkills($skills);
        $contractor2->location
            ->update([
                'lat' => 27.970679,
                'lng' => -82.437657,
            ]);

        /** @var User $contractor3 */
        $contractor3 = factory(User::class)->create([
            'street_address' => '4512 Ethan Way',
            'city' => 'Plant City',
            'state' => 'FL',
            'postal_code' => '33563',
        ]);
        $contractor3->saveSkills($skills);
        $contractor3->location
            ->update([
                'lat' => 28.013731,
                'lng' => -82.169591,
            ]);

        $response = $this->getJson(
            '/api/v1/contractor?' . http_build_query([
                'nearby' => [
                    'radius' => 15,
                    'lat' => 27.969610,
                    'lng' => -82.434497,
                ]
            ])
        )
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $response_ids = array_pluck($response->json('data'), 'id');
        /** @var User $user */
        foreach ([$contractor1, $contractor2] as $user) {
            $this->assertInArray($user->id, $response_ids);
        }
    }
}
