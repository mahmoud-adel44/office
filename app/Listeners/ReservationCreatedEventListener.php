<?php

namespace App\Listeners;

use App\Events\ReservationCreatedEvent;
use App\Notifications\NewUserReservationNotification;
use Illuminate\Support\Facades\Notification;


class ReservationCreatedEventListener
{
    public function handle(ReservationCreatedEvent $event): void
    {
        Notification::send(auth()->user(), NewUserReservationNotification::class);
        Notification::send($event->office->user, NewUserReservationNotification::class);
    }
}
