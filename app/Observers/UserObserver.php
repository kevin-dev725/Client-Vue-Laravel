<?php

namespace App\Observers;

use App\Jobs\GeocodeUserAddress;
use App\User;

class UserObserver
{
    public function saving(User $user)
    {
        if ($user->isDirty(['first_name', 'middle_name', 'last_name'])) {
            if ($user->middle_name && $user->middle_name != "undefined")
                $user->name = implode(' ', [$user->first_name, $user->middle_name, $user->last_name]);
            else 
                $user->name = implode(' ', [$user->first_name, $user->last_name]);
        }
    }

    public function saved(User $user)
    {
        if ($user->wasRecentlyCreated || $user->isDirty(['street_address', 'street_address2', 'city', 'state', 'postal_code'])) {
            //dispatch(new GeocodeUserAddress($user));  you need to re open when deploy
        }
    }
}
