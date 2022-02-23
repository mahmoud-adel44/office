<?php

namespace App\Http\Pipelines\Filtration\Office;

use App\Http\Pipelines\Pipe;
use Closure;
use function request;

class FilterByUserId implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)
            ->when(request('user_id'),
                fn($builder) => $builder->whereUserId(request('user_id'))
            );
    }
}
