<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Notifications\NewUserReservationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDueReservationsNotificationsCommand extends Command
{
    protected $signature = 'send:reservations-notifications';

    protected $description = 'Command description';

    public function handle()
    {
        Reservation::query()
            ->with('office.user')
            ->where('status', Reservation::STATUS_ACTIVE)
            ->where('start_date', now()->toDateString())
            ->each(function ($reservation) {
                Notification::send($reservation->user, new NewUserReservationNotification($reservation));
                Notification::send($reservation->office->user, new NewUserReservationNotification($reservation));
            });


        return 0;
    }
}
