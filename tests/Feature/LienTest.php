<?php

namespace Tests\Feature;

use App\Lien;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\WithAdmin;

class LienTest extends TestCase
{
    use DatabaseTransactions, WithFaker, WithAdmin;

    public function testSearchLien()
    {
        /** @var Lien $lien1 */
        $lien1 = factory(Lien::class)
            ->create([
                'state' => 'FL',
                'county' => 'Polk County',
                'owner' => 'Madisyn Burch, Ruby Shaffer'
            ]);
        /** @var Lien $lien2 */
        $lien2 = factory(Lien::class)
            ->create([
                'state' => 'FL',
                'county' => 'Polk County',
                'owner' => 'Ruby Shaffer',
            ]);
        /** @var Lien $lien3 */
        $lien3 = factory(Lien::class)
            ->create([
                'state' => 'AK',
                'county' => 'Sitka',
                'owner' => 'Cameron Gentry'
            ]);

        $this->actingAs($this->admin, 'api');
        $this->getJson('/api/v1/lien?' . http_build_query([
                'owner' => 'madisyn burch',
                'state' => 'FL',
                'county' => 'Polk'
            ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['id' => $lien1->id]
                ]
            ]);

        $this->getJson('/api/v1/lien?' . http_build_query([
                'owner' => 'madisyn burch',
                'county' => 'Broward',
                'state' => 'FL'
            ]))
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $response = $this->getJson('/api/v1/lien?' . http_build_query([
                'owner' => 'Shaffer',
                'county' => 'Polk',
                'state' => 'FL'
            ]))
            ->assertOk()
            ->assertJsonCount(2, 'data');
        $response_ids = array_pluck($response->json('data'), 'id');
        /** @var Lien $lien */
        foreach ([$lien1, $lien2] as $lien) {
            $this->assertInArray($lien->id, $response_ids);
        }
    }
}
