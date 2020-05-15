<?php

namespace Tests\Feature;

use App\Invoice;
use App\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\ApiTestCase;

class DashboardTest extends ApiTestCase
{
    use DatabaseTransactions;

    protected $role = Role::ROLE_ADMIN;

    public function testGetDashboardData()
    {
        /**
         * @var Collection $invoices
         */
        $invoices = factory(Invoice::class, 5)->states('complete')->create();
        $response = $this->authJson('get', '/api/v1/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'earnings' => [
                        'data',
                        'total'
                    ],
                    'labels',
                    'current_week',
                    'users' => [
                        'data',
                        'count'
                    ],
                    'clients' => [
                        'data',
                        'count'
                    ],
                    'reviews' => [
                        'count',
                        'data'
                    ]
                ]
            ]);
        $this->assertEquals($invoices->sum('amount'), $response->json('data.earnings.total'));
    }
}
