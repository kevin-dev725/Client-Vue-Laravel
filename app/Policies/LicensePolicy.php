<?php

namespace App\Policies;

use App\License;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LicensePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list licenses.
     *
     * @param User $user
     * @return mixed
     */
    public function index(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the license.
     *
     * @param User $user
     * @param License $license
     * @return mixed
     */
    public function view(User $user, License $license)
    {
        return true;
    }

    /**
     * Determine whether the user can create licenses.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the license.
     *
     * @param User $user
     * @param License $license
     * @return mixed
     */
    public function update(User $user, License $license)
    {
        return $user->id === $license->user_id;
    }

    /**
     * Determine whether the user can delete the license.
     *
     * @param User $user
     * @param License $license
     * @return mixed
     */
    public function delete(User $user, License $license)
    {
        return $user->id === $license->user_id;
    }

    /**
     * Determine whether the user can restore the license.
     *
     * @param User $user
     * @param License $license
     * @return mixed
     */
    public function restore(User $user, License $license)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the license.
     *
     * @param User $user
     * @param License $license
     * @return mixed
     */
    public function forceDelete(User $user, License $license)
    {
        //
    }
}
