<?php

namespace Tests;

use App\Role;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Middleware\ThrottleRequests;

abstract class ApiTestCase extends TestCase
{
    use CreatesApplication, WithFaker;

    /**
     * @var User $user
     */
    public $user;

    protected $role = Role::ROLE_USER;

    /**
     * @var array
     */
    private $authHeaders;

    public function setUp()
    {
        parent::setUp();

        $this->withoutMiddleware([
            ThrottleRequests::class
        ]);

        $this->setupUser();
    }

    private function setupUser()
    {
        if ($this->role === Role::ROLE_ADMIN) {
            $this->user = factory(User::class)->states('admin')->create();
        } else {
            $this->user = factory(User::class)->create();
        }
        $this->login();
    }

    protected function login($password = 'secret')
    {
        $this->assertDatabaseHas('users', [
            'email' => $this->user->email
        ]);
        $response = $this->json('POST', '/api/v1/token', [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->user->email,
            'password' => $password
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
        $json = $response->json();
        $this->authHeaders = [
            'Accept' => 'application/json',
            'Authorization' => $json['token_type'] . ' ' . $json['access_token']
        ];
    }

    public function authJson($method, $uri, array $data = [])
    {
        return $this->json($method, $uri, $data, $this->authHeaders);
    }
}
