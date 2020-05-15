<?php

namespace Tests\Feature\Command;

use App\User;
use Artisan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GeocodeUserAddressCommandTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function testGeocodeUserAddress()
    {
        /** @var Collection|User[] $users */
        $users = factory(User::class, 3)
            ->create();
        foreach ($users as $user) {
            $user->location->delete();
        }
        Artisan::call('clientDomain:geocode-user-address');
        foreach ($users as $user) {
            $this->assertDatabaseHas('user_locations', [
                'user_id' => $user->id,
            ]);
        }
    }
}
