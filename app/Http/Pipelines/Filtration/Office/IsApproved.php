<?php

namespace App\Http\Pipelines\Filtration\Office;

use App\Http\Pipelines\Pipe;
use App\Models\Office;
use Closure;

class IsApproved implements Pipe
{
    public function handle($request, Closure $next)
    {
        return $next($request)
            ->where('approval_status', Office::APPROVAL_APPROVED);
    }
}
