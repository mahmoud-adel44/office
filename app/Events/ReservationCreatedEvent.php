<?php

namespace App\Events;

use App\Models\Office;
use App\Models\Reservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Reservation $reservation ,
        public Office $office ,
    ){}
}
