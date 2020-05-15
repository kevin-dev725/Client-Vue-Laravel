<?php

namespace App\Observers;

use App\Client;

class ClientObserver
{
    public function saving(Client $client)
    {
        if ($client->isDirty(['first_name', 'middle_name', 'last_name'])) {
            $client->name = $client->first_name . (!empty($client->middle_name) ? ' ' . $client->middle_name : '') . ' ' . $client->last_name;
        }
    }
}
