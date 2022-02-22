<?php

namespace App\Notifications;

use App\Models\Office;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class OfficePendingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Office $office)
    {
        //
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            //
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

}
