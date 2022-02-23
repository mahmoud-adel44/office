<?php

namespace App\Http\Pipelines\Filtration\Office;

use App\Http\Pipelines\Pipe;
use Closure;
use function request;

class FilterByTags implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)
            ->when(request('tags'),
                fn($builder) => $builder->whereHas(
                    'tags',
                    fn ($builder) => $builder->whereIn('id', request('tags')),
                    '=',
                    count(request('tags'))
                )
            );
    }
}
