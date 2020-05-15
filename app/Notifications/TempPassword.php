<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TempPassword extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var
     */
    private $temp_password;

    /**
     * TempPassword constructor.
     * @param $temp_password
     */
    public function __construct($temp_password)
    {
        $this->temp_password = $temp_password;
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
        return (new MailMessage)
            ->greeting('Change Password Notification')
            ->subject('Password Changed')
            ->line('Your new temporary password: ' . $this->temp_password)
            ->line('Your temporary password will expire within 7 days. Please change it immediately');
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
