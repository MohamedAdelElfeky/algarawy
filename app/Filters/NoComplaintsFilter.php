<?php

namespace App\Filters;

use Closure;
use Illuminate\Support\Facades\Auth;

class NoComplaintsFilter
{
    public function handle($query, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $showNoComplaintedPosts = $user->userSettings()
                ->whereHas('setting', fn($q) => $q->where('key', 'show_no_complaints_posts'))
                ->value('value') ?? false;

            if ($showNoComplaintedPosts) {
                $query->where(
                    fn($q) =>
                    $q->where('user_id', $user->id)
                        ->orWhereDoesntHave('complaints')
                );
            }
        }

        return $next($query);
    }
}
