<?php

namespace App\Observers;

use App\Events\ReservationCreatedEvent;
use App\Models\Office;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReservationObserver
{
    /**
     * @throws Throwable
     */
    public function creating(Reservation $reservation): void
    {
        if (request('office')){
            throw_if(request('office')->user_id == auth()->id(),
                ValidationException::withMessages([
                    'office_id' => 'You cannot make a reservation on your own office'
                ])
            );
            throw_if(request('office')->hidden || request('office')->approval_status == Office::APPROVAL_PENDING,
                ValidationException::withMessages([
                    'office_id' => 'You cannot make a reservation on a hidden office'
                ])
            );
        }
    }

    public function created(Reservation $reservation): void
    {
    //    event(new ReservationCreatedEvent($reservation , $reservation->load('office')->office));
    }
}
