<?php

namespace App\Http\Pipelines\Filtration\Reservation;

use App\Http\Pipelines\Pipe;
use Closure;

class FilterByStatus implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->when(request('status'),
            fn($query) => $query->where('status', request('status'))
        );
    }
}
