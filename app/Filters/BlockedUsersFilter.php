<?php

namespace App\Filters;

use Closure;
use Illuminate\Support\Facades\Auth;

class BlockedUsersFilter
{
    public function handle($query, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id');
            $query->whereNotIn('user_id', $blockedUserIds);
        }

        return $next($query);
    }
}
