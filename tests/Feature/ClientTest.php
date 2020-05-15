<?php

namespace Tests\Feature;

use App\Client;
use App\Review;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\ApiTestCase;

class ClientTest extends ApiTestCase
{
    use DatabaseTransactions;

    public function testCreateClient()
    {
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'phone_number_ext' => '5555',
            'alt_phone_number' => $this->testPhoneNumber,
            'alt_phone_number_ext' => '4444',
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
            'country_id' => $this->country_id
        ];
        $response = $this->authJson('post', '/api/v1/client', $data);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('clients', array_merge($data, [
            'user_id' => $this->user->id,
            'name' => $data['first_name'] . ' ' . $data['middle_name'] . ' ' . $data['last_name'],
        ]));
        $this->assertDatabaseHas('reviews', array_merge([
            'user_id' => $this->user->id,
            'client_id' => $response->json('id'),
            'service_date' => $today = today()->toDateString(),
            'star_rating' => config('settings.import.default_initial_star_rating'),
            'payment_rating' => Review::REVIEW_RATING_NO_OPINION,
            'character_rating' => Review::REVIEW_RATING_NO_OPINION,
            'repeat_rating' => Review::REVIEW_RATING_NO_OPINION,
            'comment' => null,
        ]));
    }

    public function testCreateClientWithNoAltPhoneNumber()
    {
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'phone_number_ext' => '5555',
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
            'country_id' => $this->country_id
        ];
        $response = $this->authJson('post', '/api/v1/client', $data);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('clients', array_merge($data, [
            'user_id' => $this->user->id,
            'name' => $data['first_name'] . ' ' . $data['middle_name'] . ' ' . $data['last_name'],
        ]));
        $this->assertDatabaseHas('reviews', array_merge([
            'user_id' => $this->user->id,
            'client_id' => $response->json('id'),
            'service_date' => $today = today()->toDateString(),
            'star_rating' => config('settings.import.default_initial_star_rating'),
            'payment_rating' => Review::REVIEW_RATING_NO_OPINION,
            'character_rating' => Review::REVIEW_RATING_NO_OPINION,
            'repeat_rating' => Review::REVIEW_RATING_NO_OPINION,
            'comment' => null,
        ]));
    }

    public function testCreateClientPrependsCountryCodeToPhoneNumber()
    {
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => '6505555555',
            'phone_number_ext' => '5555',
            'alt_phone_number' => $this->testPhoneNumber,
            'alt_phone_number_ext' => '4444',
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
            'country_id' => $this->country_id
        ];
        $response = $this->authJson('post', '/api/v1/client', $data);
        $response->assertStatus(200)
            ->assertJson([
                'phone_number' => '+16505555555'
            ]);
        $this->assertDatabaseHas('clients', [
            'id' => $response->json('id'),
            'phone_number' => '+16505555555'
        ]);
    }

    public function testCreateClientCreatesInitialReview()
    {
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'phone_number_ext' => '5555',
            'alt_phone_number' => $this->testPhoneNumber,
            'alt_phone_number_ext' => '4444',
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
            'country_id' => $this->country_id,
            'review' => [
                'service_date' => $today = today()->toDateString(),
                'star_rating' => $this->faker->numberBetween(1, 5),
                'payment_rating' => $this->faker->randomElement($rating_options = Review::ratingOptions()),
                'character_rating' => $this->faker->randomElement($rating_options),
                'repeat_rating' => $this->faker->randomElement($rating_options),
                'comment' => $this->faker->sentence
            ]
        ];
        $response = $this->authJson('post', '/api/v1/client', $data);
        $response->assertStatus(200)
            ->assertJsonFragment(array_except($data, 'review'));
        $this->assertDatabaseHas('clients', array_merge(array_except($data, 'review'), [
            'user_id' => $this->user->id,
            'name' => $data['first_name'] . ' ' . $data['middle_name'] . ' ' . $data['last_name'],
        ]));
        $this->assertDatabaseHas('reviews', array_merge($data['review'], [
            'user_id' => $this->user->id,
            'client_id' => $response->json('id'),
        ]));
    }

    public function testCountryIsOptionalOnCreateClient()
    {
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'phone_number_ext' => '5555',
            'alt_phone_number' => $this->testPhoneNumber,
            'alt_phone_number_ext' => '4444',
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
        ];
        $response = $this->authJson('post', '/api/v1/client', $data);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('clients', array_merge($data, [
            'user_id' => $this->user->id
        ]));
    }

    public function testCreateClientRequiredFieldsValidation()
    {
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
        ];
        $response = $this->authJson('post', '/api/v1/client', $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'phone_number', 'street_address', 'phone_number', 'city', 'state']);
    }

    public function testCreateOrganizationClient()
    {
        $data = [
            'client_type' => Client::CLIENT_TYPE_ORGANIZATION,
            'organization_name' => $this->faker->company,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'phone_number_ext' => '5555',
            'alt_phone_number' => $this->testPhoneNumber,
            'alt_phone_number_ext' => '4444',
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
            'billing_first_name' => $this->faker->firstName,
            'billing_middle_name' => $this->faker->lastName,
            'billing_last_name' => $this->faker->lastName,
            'billing_phone_number' => $this->testPhoneNumber,
            'billing_phone_number_ext' => '5555',
            'billing_street_address' => $this->faker->streetAddress,
            'billing_street_address2' => $this->faker->streetAddress,
            'billing_city' => $this->faker->city,
            'billing_state' => 'WA',
            'billing_postal_code' => $this->faker->postcode,
            'billing_email' => $this->faker->email,
            'country_id' => $this->country_id,
        ];
        $response = $this->authJson('post', '/api/v1/client', $data);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('clients', array_merge($data, [
            'user_id' => $this->user->id
        ]));
    }

    public function testCreateOrganizationClientRequiredFieldsValidation()
    {
        $data = [
            'client_type' => Client::CLIENT_TYPE_ORGANIZATION,
        ];
        $response = $this->authJson('post', '/api/v1/client', $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['organization_name', 'phone_number', 'city', 'state', 'billing_first_name', 'billing_last_name', 'billing_street_address', 'billing_city', 'billing_state', 'first_name', 'last_name']);
    }

    public function testUpdateClient()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'phone_number_ext' => '5555',
            'alt_phone_number' => $this->testPhoneNumber,
            'alt_phone_number_ext' => '4444',
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
            'billing_first_name' => $this->faker->firstName,
            'billing_middle_name' => $this->faker->lastName,
            'billing_last_name' => $this->faker->lastName,
            'billing_phone_number' => $this->testPhoneNumber,
            'billing_phone_number_ext' => '5555',
            'billing_street_address' => $this->faker->streetAddress,
            'billing_street_address2' => $this->faker->streetAddress,
            'billing_city' => $this->faker->city,
            'billing_state' => 'WA',
            'billing_postal_code' => $this->faker->postcode,
            'billing_email' => $this->faker->email,
            'country_id' => $this->country_id
        ];
        $response = $this->authJson('post', '/api/v1/client/' . $client->id, array_merge($data, [
            '_method' => 'put'
        ]));
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('clients', array_merge($data, [
            'user_id' => $this->user->id
        ]));
    }

    public function testUpdateClientWithNoAltPhoneNumber()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'phone_number_ext' => '5555',
            'alt_phone_number' => null,
            'alt_phone_number_ext' => null,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
            'billing_first_name' => $this->faker->firstName,
            'billing_middle_name' => $this->faker->lastName,
            'billing_last_name' => $this->faker->lastName,
            'billing_phone_number' => $this->testPhoneNumber,
            'billing_phone_number_ext' => '5555',
            'billing_street_address' => $this->faker->streetAddress,
            'billing_street_address2' => $this->faker->streetAddress,
            'billing_city' => $this->faker->city,
            'billing_state' => 'WA',
            'billing_postal_code' => $this->faker->postcode,
            'billing_email' => $this->faker->email,
            'country_id' => $this->country_id
        ];
        $response = $this->authJson('post', '/api/v1/client/' . $client->id, array_merge($data, [
            '_method' => 'put'
        ]));
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('clients', array_merge($data, [
            'user_id' => $this->user->id
        ]));
    }

    public function testUpdateClientPrependsCountryCodeToPhoneNumber()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => '6505555555',
            'phone_number_ext' => '5555',
            'alt_phone_number' => '6505555555',
            'alt_phone_number_ext' => '4444',
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
            'billing_first_name' => $this->faker->firstName,
            'billing_middle_name' => $this->faker->lastName,
            'billing_last_name' => $this->faker->lastName,
            'billing_phone_number' => $this->testPhoneNumber,
            'billing_phone_number_ext' => '5555',
            'billing_street_address' => $this->faker->streetAddress,
            'billing_street_address2' => $this->faker->streetAddress,
            'billing_city' => $this->faker->city,
            'billing_state' => 'WA',
            'billing_postal_code' => $this->faker->postcode,
            'billing_email' => $this->faker->email,
            'country_id' => $this->country_id
        ];
        $response = $this->authJson('post', '/api/v1/client/' . $client->id, array_merge($data, [
            '_method' => 'put',
        ]));
        $response->assertStatus(200)
            ->assertJsonFragment(array_merge($data, [
                'phone_number' => '+16505555555',
                'alt_phone_number' => '+16505555555',
            ]));
        $this->assertDatabaseHas('clients', array_merge($data, [
            'user_id' => $this->user->id,
            'phone_number' => '+16505555555',
            'alt_phone_number' => '+16505555555',
        ]));
    }

    public function testCountryIsOptionalOnUpdateClient()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $data = [
            'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'phone_number_ext' => '5555',
            'alt_phone_number' => $this->testPhoneNumber,
            'alt_phone_number_ext' => '4444',
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => 'WA',
            'postal_code' => $this->faker->postcode,
            'email' => $this->faker->email,
            'billing_first_name' => $this->faker->firstName,
            'billing_middle_name' => $this->faker->lastName,
            'billing_last_name' => $this->faker->lastName,
            'billing_phone_number' => $this->testPhoneNumber,
            'billing_phone_number_ext' => '5555',
            'billing_street_address' => $this->faker->streetAddress,
            'billing_street_address2' => $this->faker->streetAddress,
            'billing_city' => $this->faker->city,
            'billing_state' => 'WA',
            'billing_postal_code' => $this->faker->postcode,
            'billing_email' => $this->faker->email,
        ];
        $response = $this->authJson('post', '/api/v1/client/' . $client->id, array_merge($data, [
            '_method' => 'put'
        ]));
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('clients', array_merge($data, [
            'user_id' => $this->user->id
        ]));
    }

    public function testGetClient()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $this->authJson('get', '/api/v1/client/' . $client->id)
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $client->id,
                'client_type' => $client->client_type,
                'organization_name' => $client->organization_name,
                'name' => $client->name,
                'first_name' => $client->first_name,
                'middle_name' => $client->middle_name,
                'last_name' => $client->last_name,
                'phone_number' => $client->phone_number,
                'phone_number_ext' => $client->phone_number_ext,
                'alt_phone_number' => $client->alt_phone_number,
                'alt_phone_number_ext' => $client->alt_phone_number_ext,
                'street_address' => $client->street_address,
            ]);
    }

    public function testGetClientIncludesCountry()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'country_id' => $this->country_id,
        ]);
        $response = $this->authJson('get', '/api/v1/client/' . $client->id)
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $client->id,
                'client_type' => $client->client_type,
                'organization_name' => $client->organization_name,
                'name' => $client->name,
                'first_name' => $client->first_name,
                'middle_name' => $client->middle_name,
                'last_name' => $client->last_name,
                'phone_number' => $client->phone_number,
                'phone_number_ext' => $client->phone_number_ext,
                'alt_phone_number' => $client->alt_phone_number,
                'alt_phone_number_ext' => $client->alt_phone_number_ext,
                'street_address' => $client->street_address,
                'country_id' => $client->country_id
            ]);
        $this->assertArrayHasKey('country', $response->json());
        $this->assertArraySubset([
            'id' => $client->country_id,
            'name' => $client->country->name
        ], $response->json('country'));
    }

    public function testDeleteClient()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $this->authJson('delete', '/api/v1/client/' . $client->id)
            ->assertStatus(200);
        $this->assertDatabaseMissing('clients', [
            'id' => $client->id
        ]);
    }

    public function testGetClientReviews()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        /**
         * @var Collection $reviews
         */
        $reviews = factory(Review::class, 3)->create([
            'client_id' => $client->id,
            'user_id' => $this->user->id,
        ]);
        $response = $this->authJson('get', '/api/v1/client/' . $client->id . '?include=reviews');
        $response->assertStatus(200);
        $this->assertCount(3, $response->json('reviews.data'));
        /*$this->assertArraySubset($reviews->map(function ($review) use ($client) {
            return [
                'id' => $review->id,
                'client_id' => $client->id,
                'user_id' => $this->user->id,
            ];
        }), $response->json('reviews.data'));*/
    }

    public function testGetClientsSortingWorks()
    {
        /**
         * @var Collection $clients
         */
        $clients = factory(Client::class, 3)->create([
            'user_id' => $this->user->id,
        ]);
        $clients = $clients->sortBy(function (Client $client) {
            return $client->last_name;
        })->values()->toArray();
        $response = $this->authJson('get', '/api/v1/client');
        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
        $i = 0;
        foreach ($clients as $client) {
            $this->assertEquals($client['id'], $response->json('data.' . $i++ . '.id'));
        }
    }

    public function testGetUnReviewedClients()
    {
        /**
         * @var Collection $clients
         */
        $clients = factory(Client::class, 3)->create([
            'user_id' => $this->user->id,
        ]);
        $clients = $clients->sortBy(function (Client $client) {
            return $client->last_name;
        })->values()->toArray();
        //create clients with reviews
        factory(Review::class, 3)->states('complete')->create([
            'user_id' => $this->user->id,
        ]);
        $client_from_other_user = factory(Client::class)->states('complete')->create([
            'first_name' => $clients[0]['first_name'],
            'last_name' => $clients[0]['last_name'],
            'phone_number' => $clients[0]['phone_number'],
            'email' => $clients[0]['email'],
        ]);
        factory(Review::class)->create([
            'client_id' => $client_from_other_user->id,
            'user_id' => $client_from_other_user->user_id,
        ]);
        $response = $this->authJson('get', '/api/v1/client?filter=unreviewed');
        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function testSearchClientsByFirstName()
    {
        $client_one = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => $first_name = 'John',
            'middle_name' => $middle_name = 'Juan',
            'last_name' => $last_name = 'Doe',
            'name' => $first_name . ' ' . $middle_name . ' ' . $last_name,
            'city' => 'South Masonland',
            'street_address' => '293 Angelita Estate'
        ]);
        $client_two = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => $first_name = 'John',
            'middle_name' => $middle_name = 'Jason',
            'last_name' => $last_name = 'Hummer',
            'name' => $first_name . ' ' . $middle_name . ' ' . $last_name,
            'city' => 'Port Nicola',
            'street_address' => '7005 Berniece Turnpike'
        ]);
        $client_three = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => $first_name = 'Pierre',
            'middle_name' => $middle_name = 'Kaz',
            'last_name' => $last_name = 'Munos',
            'name' => $first_name . ' ' . $middle_name . ' ' . $last_name,
            'city' => 'Dibbertview',
            'street_address' => '526 Tillman Terrace'
        ]);
        $response = $this->authJson('get', '/api/v1/client', [
            'keyword' => 'John'
        ]);
        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
        $this->assertEquals($client_one->id, $response->json('data.0.id'));
        $this->assertEquals($client_two->id, $response->json('data.1.id'));
    }

    public function testSearchClientByLastName()
    {
        $client_one = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => $first_name = 'John',
            'middle_name' => $middle_name = 'Kaz',
            'last_name' => $last_name = 'Doe',
            'name' => $first_name . ' ' . $middle_name . ' ' . $last_name,
            'city' => 'South Masonland',
            'street_address' => '293 Angelita Estate'
        ]);
        $client_two = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => $first_name = 'Jeffrey',
            'middle_name' => $middle_name = 'Foo',
            'last_name' => $last_name = 'Hummer',
            'name' => $first_name . ' ' . $middle_name . ' ' . $last_name,
            'city' => 'Port Nicola',
            'street_address' => '7005 Berniece Turnpike'
        ]);
        $client_three = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => $first_name = 'Pierre',
            'middle_name' => $middle_name = 'Bar',
            'last_name' => $last_name = 'Doe',
            'name' => $first_name . ' ' . $middle_name . ' ' . $last_name,
            'city' => 'Dibbertview',
            'street_address' => '526 Tillman Terrace'
        ]);
        $response = $this->authJson('get', '/api/v1/client', [
            'keyword' => 'Doe'
        ]);
        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
        $this->assertEquals($client_one->id, $response->json('data.0.id'));
        $this->assertEquals($client_three->id, $response->json('data.1.id'));
        /*$this->assertArraySubset([
            [
                'id' => $client_one->id,
                'first_name' => $client_one->first_name,
                'last_name' => $client_one->last_name,
            ],
            [
                'id' => $client_three->id,
                'first_name' => $client_three->first_name,
                'last_name' => $client_three->last_name,
            ],
        ], $response->json('data'));*/
    }

    public function testSearchClientByStreetAddress()
    {
        $client_one = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'city' => 'South Masonland',
            'street_address' => '293 Angelita Estate'
        ]);
        $client_two = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => 'Jeffrey',
            'last_name' => 'Hummer',
            'city' => 'Port Nicola',
            'street_address' => '7005 Berniece Turnpike'
        ]);
        $client_three = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => 'Pierre',
            'last_name' => 'Doe',
            'city' => 'Dibbertview',
            'street_address' => '526 Tillman Terrace'
        ]);
        $response = $this->authJson('get', '/api/v1/client', [
            'keyword' => '526 Tillman'
        ]);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertArraySubset([
            [
                'id' => $client_three->id,
                'first_name' => $client_three->first_name,
                'last_name' => $client_three->last_name,
            ],
        ], $response->json('data'));
    }

    public function testSearchClientByCity()
    {
        $client_one = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'city' => 'South Masonland',
            'street_address' => '293 Angelita Estate'
        ]);
        $client_two = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => 'Jeffrey',
            'last_name' => 'Hummer',
            'city' => 'Port Nicola',
            'street_address' => '7005 Berniece Turnpike'
        ]);
        $client_three = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'first_name' => 'Pierre',
            'last_name' => 'Doe',
            'city' => 'Dibbertview',
            'street_address' => '526 Tillman Terrace'
        ]);
        $response = $this->authJson('get', '/api/v1/client', [
            'keyword' => 'Port Nicola'
        ]);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertArraySubset([
            [
                'id' => $client_two->id,
                'first_name' => $client_two->first_name,
                'last_name' => $client_two->last_name,
            ],
        ], $response->json('data'));
    }

    public function testUpdateClientWithNameAddressAndContactInformation()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => '+16505553322',
            'phone_number_ext' => '5555',
            'street_address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'postal_code' => $this->faker->postcode,
            'organization_name' => $this->faker->company
        ];

        $response = $this->authJson('post', '/api/v1/client/' . $client->id . '?basic_info=true', array_merge($data, [
            '_method' => 'put'
        ]));

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('clients', array_merge($data, [
            'user_id' => $this->user->id
        ]));
    }
}
