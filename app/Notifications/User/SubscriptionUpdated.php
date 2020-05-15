<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionUpdated extends Notification
{
    use Queueable;

    public $method;

    /**
     * Create a new notification instance.
     *
     * @param $method
     */
    public function __construct($method)
    {
        $this->method = $method;
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
        $message = (new MailMessage)
            ->greeting("You have successfully {$this->method}ed your subscription!");

        if ($this->method === 'resume') {
            $message->line('You can now enjoy our services again.');
        } else {
            $message->line('To resume back your account, login to your account and choose resume subscription.')
                ->action('Login', url('/login'));
        }

        return $message;
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
