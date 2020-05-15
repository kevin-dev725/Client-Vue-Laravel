<?php

namespace Tests\Traits;

use App\User;

trait WithAdmin
{
    /**
     * @var User
     */
    protected $admin;

    protected function setupAdmin()
    {
        $this->admin = factory(User::class)
            ->state('admin')
            ->create();
    }
}
