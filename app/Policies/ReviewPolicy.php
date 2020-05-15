<?php

namespace App\Policies;

use App\Review;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization, AdminHasAccessTrait;

    /**
     * Determine whether the user can view the review.
     *
     * @param User $user
     * @param Review $review
     * @return mixed
     */
    public function view(User $user, Review $review)
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can create reviews.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can update the review.
     *
     * @param User $user
     * @param Review $review
     * @return mixed
     */
    public function update(User $user, Review $review)
    {
        return $user->ownsReview($review);
    }

    /**
     * Determine whether the user can delete the review.
     *
     * @param User $user
     * @param Review $review
     * @return mixed
     */
    public function delete(User $user, Review $review)
    {
        return $user->ownsReview($review);
    }
}
