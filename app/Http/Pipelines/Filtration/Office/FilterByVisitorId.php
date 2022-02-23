<?php

namespace App\Http\Pipelines\Filtration\Office;

use App\Http\Pipelines\Pipe;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use function request;

class FilterByVisitorId implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->when(request('visitor_id'),
            fn (Builder $query) =>
            $query->whereRelation('reservations', 'user_id', request('visitor_id'))
        );

    }
}
