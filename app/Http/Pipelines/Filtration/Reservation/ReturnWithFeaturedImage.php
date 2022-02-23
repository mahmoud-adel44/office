<?php

namespace App\Http\Pipelines\Filtration\Reservation;

use App\Http\Pipelines\Pipe;
use Closure;

class ReturnWithFeaturedImage implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->with(['office.featuredImage']);
    }
}
