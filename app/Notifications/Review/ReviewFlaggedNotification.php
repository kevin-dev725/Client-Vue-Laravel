<?php

namespace App\Notifications\Review;

use App\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewFlaggedNotification extends Notification
{
    use Queueable;
    /**
     * @var Review
     */
    public $review;

    /**
     * Create a new notification instance.
     *
     * @param Review $review
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->markdown('emails.review.review-flagged', [
            'review' => $this->review
        ])
            ->subject("Flagged Review from {$this->review->user->email}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
