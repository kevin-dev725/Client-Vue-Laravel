<?php

namespace App\Notifications\User;

use App\ClientImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvalidImportNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * @var ClientImport
     */
    public $clientImport;

    /**
     * Create a new notification instance.
     *
     * @param ClientImport $clientImport
     */
    public function __construct(ClientImport $clientImport)
    {
        $this->clientImport = $clientImport;
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
        return (new MailMessage)->markdown('user.invalid_import_notification', [
            'import' => $this->clientImport
        ]);
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
