<?php

namespace Tests\Feature;

use App\Notifications\PasswordReset;
use DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Tests\ApiTestCase;

class AccountTest extends ApiTestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
    }

    public function testGetAuthenticatedUser()
    {
        $response = $this->authJson('get', '/api/v1/auth/user');
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $this->user->id,
                'name' => $this->user->name,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
            ]);
    }

    public function testUpdateContactInformation()
    {
        $response = $this->authJson('post', '/api/v1/auth/user/update-contact', [
            'phone_number' => $phone_number = $this->testPhoneNumber,
            'phone_number_ext' => '5555',
            'street_address' => $line1 = $this->faker->streetAddress,
            'street_address2' => $line2 = $this->faker->address,
            'city' => $city = $this->faker->city,
            'state' => 'WA',
            'postal_code' => $postal_code = $this->faker->postcode,
            'email' => $this->user->email,
            'business_url' => $business_url = $this->faker->url,
            'facebook_url' => $facebook_url = $this->faker->url,
            'twitter_url' => $twitter_url = $this->faker->url,
//            'country_id' => $this->country_id
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'phone_number' => $phone_number,
                'phone_number_ext' => '5555',
                'street_address' => $line1,
                'street_address2' => $line2,
                'city' => $city,
                'state' => 'WA',
                'postal_code' => $postal_code,
                'email' => $this->user->email,
                'business_url' => $business_url,
                'facebook_url' => $facebook_url,
                'twitter_url' => $twitter_url,
            ]);
        $this->assertDatabaseHas('users', [
            'phone_number' => $phone_number,
            'phone_number_ext' => '5555',
            'street_address' => $line1,
            'street_address2' => $line2,
            'city' => $city,
            'state' => 'WA',
            'postal_code' => $postal_code,
            'email' => $this->user->email,
            'business_url' => $business_url,
            'facebook_url' => $facebook_url,
            'twitter_url' => $twitter_url,
        ]);
    }

    public function testUpdateContactInformationRequiredFieldsValidation()
    {
        $response = $this->authJson('post', '/api/v1/auth/user/update-contact', [

        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone_number', 'street_address', 'city', 'state', 'email']);
    }

    public function testUpdateProfileInformation()
    {
        $response = $this->authJson('post', '/api/v1/auth/user/update-profile', $data = [
            'first_name' => $first_name = $this->faker->firstName,
            'middle_name' => $middle_name = $this->faker->firstName,
            'last_name' => $last_name = $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->testPhoneNumber,
            'street_address' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'business_url' => $this->faker->url,
            'facebook_url' => $this->faker->url,
            'twitter_url' => $this->faker->url,
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('users', $data);
    }

    public function testUpdateContractorFields()
    {
        $this->authJson('post', '/api/v1/auth/user/update-profile', $data = [
            'overview' => $this->faker->text,
            'license_number' => $this->faker->uuid,
            'is_insured' => $this->faker->boolean,
        ])
            ->assertOk()
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('users', array_merge(
            $data,
            $this->user->only('id')
        ));
    }

    public function testUpdateSkills()
    {
        $skills = [$this->faker->unique()->word, $this->faker->unique()->word, $this->faker->unique()->word];
        $this->actingAs($this->user, 'api');
        $this->postJson('/api/v1/auth/user/update-profile', $data = [
            'skills' => implode(',', $skills),
        ])
            ->assertOk()
            ->assertJson($data);
        foreach ($skills as $skill) {
            $this->assertDatabaseHas('skills', [
                'name' => $skill
            ]);
            $skill = DB::table('skills')->where('name', $skill)->first();
            $this->assertDatabaseHas('user_skills', [
                'user_id' => $this->user->id,
                'skill_id' => $skill->id,
            ]);
        }
    }

    public function testUpdateProfileInformationRequiredFieldsValidation()
    {
        $response = $this->authJson('post', '/api/v1/auth/user/update-profile', [
            'first_name' => null,
            'last_name' => null,
            'email' => null,
            'phone_number' => null,
            'city' => null,
            'state' => null,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'phone_number', 'city', 'state']);
    }

    public function testUpdatePassword()
    {
        $response = $this->authJson('post', '/api/v1/auth/user/change-password', [
            'password' => $new_password = 'new-secret',
            'password_confirmation' => $new_password
        ]);
        $response->assertStatus(200);
        $this->login($new_password);
    }

    public function testUpdatePasswordRequiredFieldsValidation()
    {
        $response = $this->authJson('post', '/api/v1/auth/user/change-password');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function testUpdateAvatar()
    {
        $response = $this->authJson('post', '/api/v1/auth/user/avatar', [
            'avatar' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('avatar_path'));
    }

    public function testPasswordReset()
    {
        Notification::fake();

        $this->postJson('/api/v1/auth/password/request-reset', [
            'email' => $this->user->email
        ])->assertStatus(200);

        $token = '';
        $email = '';

        Notification::assertSentTo(
            $this->user,
            PasswordReset::class,
            function ($notification, $channels) use (&$token, &$email) {
                $token = $notification->token;
                $email = $notification->email;
                return true;
            }
        );

        $this->postJson('/api/v1/auth/password/verify-token', [
            'token' => $token,
            'email' => $email,
        ])->assertStatus(200);

        $response = $this->postJson('/api/v1/auth/password/reset', [
            'email' => $email,
            'token' => $token,
            'password' => $newPassword = 'new-password',
            'password_confirmation' => $newPassword
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('password_resets', [
            'email' => $email,
            'token' => $token
        ]);
        $this->login($newPassword);
    }
}
