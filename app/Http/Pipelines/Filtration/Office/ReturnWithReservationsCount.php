<?php

namespace App\Http\Pipelines\Filtration\Office;

use App\Http\Pipelines\Pipe;
use App\Models\Reservation;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class ReturnWithReservationsCount implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->withCount([
            'reservations' =>
                fn(Builder $query) =>
                $query->whereStatus(Reservation::STATUS_ACTIVE)
        ]);
    }
}
