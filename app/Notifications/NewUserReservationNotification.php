<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

class NewUserReservationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
//        public Reservation $reservation
    ){}

    public function via($notifiable): array
    {
        return ['database'];
    }

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
