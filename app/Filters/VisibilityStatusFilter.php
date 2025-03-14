<?php

namespace App\Filters;

use Closure;
use Illuminate\Support\Facades\Auth;

class VisibilityStatusFilter
{
    public function handle($query, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            $query->visibilityStatus();
        }

        return $next($query);
    }
}
