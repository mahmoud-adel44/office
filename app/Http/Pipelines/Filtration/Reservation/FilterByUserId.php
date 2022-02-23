<?php

namespace App\Http\Pipelines\Filtration\Reservation;

use App\Http\Pipelines\Pipe;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByUserId implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->whereBelongsTo(auth()->user());
    }
}
