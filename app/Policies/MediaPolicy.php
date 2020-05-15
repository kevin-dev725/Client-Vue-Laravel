<?php

namespace App\Policies;

use App\Media;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $action)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the media.
     *
     * @param User $user
     * @param Media $media
     * @return mixed
     */
    public function view(User $user, Media $media)
    {
        return true;
    }

    /**
     * Determine whether the user can create media.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the media.
     *
     * @param User $user
     * @param Media $media
     * @return mixed
     */
    public function update(User $user, Media $media)
    {
        // for gallery photos
        if ($media->model_type === User::class && $media->model_id === $user->id && $media->collection_name === User::MEDIA_COLLECTION_GALLERY) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the media.
     *
     * @param User $user
     * @param Media $media
     * @return mixed
     */
    public function delete(User $user, Media $media)
    {
        // for gallery photos
        if ($media->model_type === User::class && $media->model_id === $user->id && $media->collection_name === User::MEDIA_COLLECTION_GALLERY) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the media.
     *
     * @param User $user
     * @param Media $media
     * @return mixed
     */
    public function restore(User $user, Media $media)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the media.
     *
     * @param User $user
     * @param Media $media
     * @return mixed
     */
    public function forceDelete(User $user, Media $media)
    {
        //
    }
}
