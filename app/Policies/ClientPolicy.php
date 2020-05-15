<?php

namespace App\Policies;

use App\Client;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization, AdminHasAccessTrait;

    /**
     * Determine whether the user can view the client.
     *
     * @param User $user
     * @param Client $client
     * @return mixed
     */
    public function view(User $user, Client $client)
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can create clients.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can update the client.
     *
     * @param User $user
     * @param Client $client
     * @return mixed
     */
    public function update(User $user, Client $client)
    {
        return $user->isUser() && $client->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the client.
     *
     * @param User $user
     * @param Client $client
     * @return mixed
     */
    public function delete(User $user, Client $client)
    {
        return $user->isUser() && $client->user_id === $user->id;
    }

    /**
     * @param User $user
     * @param Client $client
     * @return bool
     */
    public function review(User $user, Client $client)
    {
        return $user->isUser() && $client->user_id === $user->id;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function storeWithReview(User $user)
    {
        return $user->isUser();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function import(User $user)
    {
        return $user->isUser();
    }
}
