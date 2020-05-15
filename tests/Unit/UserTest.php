<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testUserHasInvoices()
    {
        $user = factory(User::class)->create();
        $this->assertTrue(method_exists($user, 'invoice_models'), 'Method invoice_models does not exist.');
        $this->assertInstanceOf(HasMany::class, $user->invoice_models(), 'Method invoice_models does not return ' . HasMany::class . ' instance.');
        $this->assertInstanceOf(Collection::class, $user->invoice_models);
    }
}
