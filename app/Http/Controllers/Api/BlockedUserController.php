<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockedUserController extends Controller
{
    public function toggleBlock(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'blocked_user_id' => 'required|exists:users,id',
        ]);

        $existingRecord = BlockedUser::where([
            'user_id' => $user->id,
            'blocked_user_id' => $request->input('blocked_user_id'),
        ])->first();

        if ($existingRecord) {
            // Record exists, so delete it (unblock user)
            $existingRecord->delete();
            $message = 'User unblocked successfully';
            $statusCode = 200;
        } else {
            // Record doesn't exist, so create it (block user)
            BlockedUser::create([
                'user_id' => $user->id,
                'blocked_user_id' => $request->input('blocked_user_id'),
            ]);
            $message = 'User blocked successfully';
            $statusCode = 201;
        }

        return response()->json(['message' => $message], $statusCode);
    }

    public function getBlockedUsers()
    {
        $user = Auth::user();

        $blockedUsers = BlockedUser::where('user_id', $user->id)            
            ->get();

        return response()->json(['blockedUsers' => $blockedUsers]);
    }
}
