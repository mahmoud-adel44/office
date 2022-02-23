<?php

namespace App\Http\Pipelines\Filtration\Reservation;

use App\Http\Pipelines\Pipe;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByOfficeForUser implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->whereRelation('office', 'user_id', '=', auth()->id());
    }
}
