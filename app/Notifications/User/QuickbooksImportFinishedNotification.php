<?php

namespace App\Notifications\User;

use App\QuickbooksImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuickbooksImportFinishedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * @var QuickbooksImport
     */
    private $import;

    /**
     * Create a new notification instance.
     *
     * @param QuickbooksImport $import
     */
    public function __construct(QuickbooksImport $import)
    {
        $this->import = $import;
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
        return (new MailMessage)->subject($this->getSubject())
            ->markdown('user.quickbooks_import_finished', [
                'import' => $this->import
            ]);
    }

    /**
     * @return string
     */
    private function getSubject()
    {
        $subject = 'Quickbooks Import Finished';
        switch ($this->import->status) {
            case QuickbooksImport::STATUS_ERROR:
                return 'Quickbooks Import Error';
            case QuickbooksImport::STATUS_FINISHED_WITH_ERROR:
                return $subject . ' With Issues';
        }
        return $subject;
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
