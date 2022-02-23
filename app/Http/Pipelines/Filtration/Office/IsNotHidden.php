<?php

namespace App\Http\Pipelines\Filtration\Office;

use App\Http\Pipelines\Pipe;
use App\Models\Office;
use Closure;
use function auth;
use function request;

class IsNotHidden implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)->when(request('user_id') && auth()->user() && request('user_id') == auth()->id(),
                fn($builder) => $builder,
                fn($builder) => $builder->where('approval_status', Office::APPROVAL_APPROVED)->where('hidden', false)
            );
    }
}
