<?php

namespace App\Observers;

use App\Models\Office;
use App\Models\Reservation;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReservationObserver
{
    /**
     * @throws Throwable
     */
    public function creating(Reservation $reservation): void
    {
        if (request('office')) {
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

    /**
     * @throws Throwable
     */
    public function updating(Reservation $reservation): void
    {
        throw_if($reservation->user_id != auth()->id() ||
            $reservation->status === Reservation::STATUS_CANCELLED ||
            $reservation->start_date < now()->toDateString(),
            ValidationException::withMessages([
                'reservation' => 'You cannot cancel this reservation'
            ])
        );
    }
}
