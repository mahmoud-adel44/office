<?php

namespace App\Http\Pipelines\Filtration\Office;

use App\Http\Pipelines\Pipe;
use Closure;

class ReturnWithImages implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->with('images');
    }
}
