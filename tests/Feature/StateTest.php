<?php

namespace Tests\Feature;

use App\State;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\WithAdmin;

class StateTest extends TestCase
{
    use DatabaseTransactions, WithAdmin;

    public function testGetStates()
    {
        $this->actingAs($this->admin, 'api');

        $this->getJson('/api/v1/state')
            ->assertSuccessful()
            ->assertJsonCount(State::where('country_code', 'US')->count(), 'data');
    }
}
