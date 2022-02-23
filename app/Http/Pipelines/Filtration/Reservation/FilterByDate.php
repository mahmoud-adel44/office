<?php

namespace App\Http\Pipelines\Filtration\Reservation;

use App\Http\Pipelines\Pipe;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByDate implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->when(request('from_date') && request('to_date'),
            fn(Builder $query) => $query->betweenDates(request('from_date'), request('to_date'))
        );
    }
}
