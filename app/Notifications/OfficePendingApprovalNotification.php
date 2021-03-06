<?php

namespace App\Notifications;

use App\Models\Office;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use JetBrains\PhpStorm\ArrayShape;

class OfficePendingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Office $office
    ){}

    public function via($notifiable): array
    {
        return ['database'];
    }

    #[ArrayShape(['message' => "string"])]
    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'new reservation created'
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'new reservation created'
        ];
    }


}
