<?php

namespace App\Http\Pipelines\Filtration\Reservation;

use App\Http\Pipelines\Pipe;
use Closure;

class FilterByOfficeId implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->when(request('office_id'),
            fn($query) => $query->where('office_id', request('office_id'))
        );
    }
}
