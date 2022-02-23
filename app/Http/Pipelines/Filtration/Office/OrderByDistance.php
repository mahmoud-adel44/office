<?php

namespace App\Http\Pipelines\Filtration\Office;

use App\Http\Pipelines\Pipe;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use function request;

class OrderByDistance implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)
            ->when(
                request('lat') && request('lng'),
                fn (Builder $builder) => $builder->nearestTo(request('lat'), request('lng')),
                fn (Builder $builder) => $builder->orderBy('id', 'ASC')
            );
    }
}
