<?php

namespace App\Policies;

use App\Plan;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
{
    use HandlesAuthorization, AdminHasAccessTrait;

    /**
     * Determine whether the user can view the plan.
     *
     * @param User $user
     * @param Plan $plan
     * @return mixed
     */
    public function view(User $user, Plan $plan)
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can create plans.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the plan.
     *
     * @param User $user
     * @param Plan $plan
     * @return mixed
     */
    public function update(User $user, Plan $plan)
    {
        //
    }

    /**
     * Determine whether the user can delete the plan.
     *
     * @param User $user
     * @param Plan $plan
     * @return mixed
     */
    public function delete(User $user, Plan $plan)
    {
        //
    }

    public function subscribe(User $user, Plan $plan)
    {
        return $user->isUser();
    }
}
