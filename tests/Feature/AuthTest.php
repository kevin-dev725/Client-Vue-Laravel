<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Cashier\Subscription;
use Tests\TestCase;
use Tests\Traits\WithTestCoupon;

class AuthTest extends TestCase
{
    use DatabaseTransactions, WithFaker, WithTestCoupon;
    /**
     * @var User
     */
    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testLogin()
    {
        $this->assertDatabaseHas('users', [
            'email' => $this->user->email
        ]);
        $response = $this->json('POST', '/api/v1/token', [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->user->email,
            'password' => 'secret'
        ], [
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'token_type' => 'Bearer',
            ])
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ]);
    }

    public function testLoginRequiresApiKeys()
    {
        $response = $this->json('POST', '/api/v1/token', [
            'grant_type' => 'password'
        ], [
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(400)
            ->assertJsonFragment([
                "error" => "invalid_request",
                "message" => "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed.",
                "hint" => "Check the `client_id` parameter",
            ]);
    }

    public function testLoginInvalidKeysValidation()
    {
        $response = $this->json('POST', '/api/v1/token', [
            'grant_type' => 'password',
            'client_id' => 'invalid client id',
            'client_secret' => 'invalid client secret',
        ], [
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(401)
            ->assertJsonFragment([
                "error" => "invalid_client",
                "message" => "Client authentication failed",
            ]);
    }

    public function testLoginUsingInvalidCredentials()
    {
        $response = $this->json('POST', '/api/v1/token', [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->faker->email,
            'password' => $this->faker->password
        ], [
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(401)
            ->assertJsonFragment([
                "error" => "invalid_credentials",
            ]);
    }

    public function testRegister()
    {
        $response = $this->json('POST', '/register', $data = [
            'account_type' => User::ACCOUNT_TYPE_INDIVIDUAL,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'alt_phone_number' => $this->testPhoneNumber,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'postal_code' => $this->faker->postcode,
            'card_token' => 'tok_visa',
            'plan_interval' => 'yearly',
//            'country_id' => $this->country_id,
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment($clean_data = array_merge(
                array_except($data, ['password', 'password_confirmation', 'card_token', 'plan_interval']),
                [
                    'is_free_account' => false,
                ]
            ));
        $this->assertDatabaseHas('users', $clean_data);
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $response->json('user.id'),
            'name' => 'main',
            'stripe_plan' => config('services.stripe.yearly_plan.id'),
            'quantity' => 1,
        ]);
    }

    public function testRegisterGeocodesUserAddress()
    {
        $response = $this->json('POST', '/register', $data = [
            'account_type' => User::ACCOUNT_TYPE_INDIVIDUAL,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'alt_phone_number' => $this->testPhoneNumber,
            'street_address' => '15880',
            'street_address2' => 'SW Rainbow Rd',
            'city' => 'Terrebonne',
            'state' => 'OR',
            'postal_code' => '97760',
            'card_token' => 'tok_visa',
            'plan_interval' => 'yearly',
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('user_locations', [
            'user_id' => $response->json('user.id'),
        ]);
    }

    public function testRegisterThroughApi()
    {
        config([
            'settings.free_account_on_register_enabled' => true,
        ]);
        $response = $this->json('POST', '/api/v1/auth/register', $data = [
            'account_type' => User::ACCOUNT_TYPE_INDIVIDUAL,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'alt_phone_number' => $this->testPhoneNumber,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'postal_code' => $this->faker->postcode,
        ]);
        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
        $this->assertDatabaseHas('users', array_merge(
            array_except($data, ['password', 'password_confirmation']),
            [
                'is_free_account' => true,
            ]
        ));
        $user = User::query()->where('email', $data['email'])
            ->first();
        $this->assertNull($user->trial_ends_at);
        $this->assertDatabaseMissing('subscriptions', [
            'user_id' => $response->json('user.id')
        ]);
    }

    public function testRegisterThroughApiGeocodesUserAddress()
    {
        config([
            'settings.free_account_on_register_enabled' => true,
        ]);
        $response = $this->json('POST', '/api/v1/auth/register', $data = [
            'account_type' => User::ACCOUNT_TYPE_INDIVIDUAL,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'alt_phone_number' => $this->testPhoneNumber,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'postal_code' => $this->faker->postcode,
        ])
            ->assertSuccessful();
        /** @var User $user */
        $user = User::where('email', $data['email'])
            ->first();
        $this->assertDatabaseHas('user_locations', [
            'user_id' => $user->id,
        ]);
    }

    public function testRegisterFreeAccount()
    {
        config([
            'settings.free_account_on_register_enabled' => true,
        ]);
        $response = $this->json('POST', '/register', $data = [
            'account_type' => User::ACCOUNT_TYPE_INDIVIDUAL,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'alt_phone_number' => $this->testPhoneNumber,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'postal_code' => $this->faker->postcode,
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment(array_merge(
                array_except($data, ['password', 'password_confirmation']),
                [
                    'is_free_account' => true,
                ]
            ));
        $this->assertDatabaseHas('users', array_merge(
            array_except($data, ['password', 'password_confirmation']),
            [
                'is_free_account' => true,
            ]
        ));
        $user = User::query()->where('email', $data['email'])
            ->first();
        $this->assertNull($user->trial_ends_at);
        $this->assertDatabaseMissing('subscriptions', [
            'user_id' => $response->json('user.id')
        ]);
    }

    public function testRegisterWithCouponCode()
    {
        $response = $this->json('POST', '/register', $data = [
            'account_type' => User::ACCOUNT_TYPE_INDIVIDUAL,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'alt_phone_number' => $this->testPhoneNumber,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'postal_code' => $this->faker->postcode,
            'card_token' => 'tok_visa',
            'plan_interval' => 'yearly',
            'coupon_code' => $this->testCouponId,
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment($clean_data = array_except($data, ['password', 'password_confirmation', 'card_token', 'plan_interval', 'coupon_code']));
        $this->assertDatabaseHas('users', $clean_data);
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $response->json('user.id'),
            'name' => 'main',
            'stripe_plan' => config('services.stripe.yearly_plan.id'),
            'quantity' => 1,
        ]);
        $subscription = Subscription::query()
            ->where('user_id', $response->json('user.id'))
            ->first();
        $stripe_subscription = \Stripe\Subscription::retrieve($subscription->stripe_id);
        $this->assertNotNull($stripe_subscription->discount);
        $this->assertEquals($stripe_subscription->discount->coupon->id, $this->testCouponId);
    }

    public function testRegisterDoesNotAcceptInvalidCouponCode()
    {
        $this->json('POST', '/register', $data = [
            'account_type' => User::ACCOUNT_TYPE_INDIVIDUAL,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'alt_phone_number' => $this->testPhoneNumber,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'postal_code' => $this->faker->postcode,
            'card_token' => 'tok_visa',
            'plan_interval' => 'yearly',
            'coupon_code' => 'invalid coupon code',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['coupon_code']);
    }

    public function testEmailMustBeUniqueOnRegister()
    {
        $user = factory(User::class)->create();
        $response = $this->json('POST', '/register', $data = [
            'account_type' => User::ACCOUNT_TYPE_INDIVIDUAL,
            'email' => $user->email,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'alt_phone_number' => $this->testPhoneNumber,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'postal_code' => $this->faker->postcode,
            'card_token' => 'tok_visa',
            'plan_interval' => 'monthly',
//            'country_id' => $this->country_id,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonFragment([
                'errors' => [
                    'email' => ['The email has already been taken.']
                ]
            ]);
    }

    public function testRegisterRequiredFieldsValidation()
    {
        $response = $this->json('POST', '/register');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['account_type', 'email', 'password', 'first_name', 'last_name', 'phone_number', 'street_address', 'city', 'state', 'postal_code', 'card_token', 'plan_interval']);
    }

    public function testRegisterFreeAccountHasRequiredFields()
    {
        config([
            'settings.free_account_on_register_enabled' => true,
        ]);
        $response = $this->json('POST', '/register');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['account_type', 'email', 'password', 'first_name', 'last_name', 'phone_number', 'street_address', 'city', 'state', 'postal_code'])
            ->assertJsonMissingValidationErrors(['card_token', 'plan_interval']);
    }

    public function testLoginAfterRegister()
    {
        $response = $this->json('POST', '/register', $data = [
            'account_type' => User::ACCOUNT_TYPE_INDIVIDUAL,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->testPhoneNumber,
            'alt_phone_number' => $this->testPhoneNumber,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'postal_code' => $this->faker->postcode,
            'card_token' => 'tok_visa',
            'plan_interval' => 'monthly'
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment(array_except($data, ['password', 'password_confirmation', 'card_token', 'plan_interval']));

        $response = $this->json('POST', '/api/v1/token', [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $data['email'],
            'password' => $data['password']
        ], [
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'token_type' => 'Bearer',
            ])
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ]);
    }
}
