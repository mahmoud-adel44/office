<?php

namespace App\Http\Pipelines\Filtration\Office;

use App\Http\Pipelines\Pipe;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use function request;

class FilterByHostId implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->when(request('host_id'),
            fn(Builder $query) =>
            $query->whereUserId(request('host_id'))
        );
    }
}
