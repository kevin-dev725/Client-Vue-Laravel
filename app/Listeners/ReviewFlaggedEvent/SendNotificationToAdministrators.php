<?php

namespace App\Listeners\ReviewFlaggedEvent;

use App\Events\ReviewFlaggedEvent;
use App\Notifications\Review\ReviewFlaggedNotification;
use App\Role;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendNotificationToAdministrators implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ReviewFlaggedEvent $event
     * @return void
     */
    public function handle(ReviewFlaggedEvent $event)
    {
        Notification::send(
            User::query()->where('role_id', Role::ROLE_ADMIN)->get(),
            new ReviewFlaggedNotification($event->review)
        );
    }
}
