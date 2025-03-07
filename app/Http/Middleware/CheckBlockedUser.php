<?php

namespace App\Http\Middleware;

use App\Domain\Models\BlockedUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            // Check if the user is blocked
            $blockedUser = BlockedUser::where('user_id', $user->id)
                ->where('blocked_user_id', $request->route('user'))
                ->first();

            if ($blockedUser) {
                return response()->json(['error' => 'You are blocked by this user.'], 403);
            }
        }

        return $next($request);
    }
}
