<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization, AdminHasAccessTrait;

    public function index()
    {
        //
    }
}
