<?php

namespace App\Policies;

use App\Trainer;
use Illuminate\Foundation\Auth\User;

trait AdminHasAccessTrait
{
    /**
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
