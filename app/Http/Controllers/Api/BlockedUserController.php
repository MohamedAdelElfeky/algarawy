<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockedUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function toggleBlock(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'blocked_user_id' => 'required|exists:users,id',
        ]);

        $userBlock = User::find($request->input('blocked_user_id'));
        $existingRecord = BlockedUser::where([
            'user_id' => $user->id,
            'blocked_user_id' => $request->input('blocked_user_id'),
        ])->first();

        if ($existingRecord) {
            // Record exists, so delete it (unblock user)
            $existingRecord->delete();
            $message = 'User unblocked successfully';
            $isBlocked = false;
            $statusCode = 200;
        } else {
            // Record doesn't exist, so create it (block user)
            BlockedUser::create([
                'user_id' => $user->id,
                'blocked_user_id' => $request->input('blocked_user_id'),
            ]);
            $message = 'User blocked successfully';
            $isBlocked = true;
            $statusCode = 201;
        }

        return response()->json([
            'message' => $message,
            'is_blocked' => $isBlocked,
        ], $statusCode);
    }


    public function getBlockedUsers()
    {
        $user = Auth::user();

        $blockedUsers = BlockedUser::where('user_id', $user->id)->get();

        // Assuming BlockedUser has a relationship with User to fetch the blocked user details
        $blockedUsersDetails = $blockedUsers->map(function ($blockedUser) {
            return $blockedUser->blockedUser; // Adjust this based on your relationship naming
        });

        return response()->json(['data' => UserResource::collection($blockedUsersDetails)]);
    }
}
