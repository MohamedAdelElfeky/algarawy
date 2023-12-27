<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use Illuminate\Http\Request;

class BlockedUserController extends Controller
{
    public function blockUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'blocked_user_id' => 'required|exists:users,id',
        ]);

        BlockedUser::create($request->all());

        return response()->json(['message' => 'User blocked successfully'], 201);
    }

    public function unblockUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'blocked_user_id' => 'required|exists:users,id',
        ]);

        BlockedUser::where($request->only(['user_id', 'blocked_user_id']))->delete();

        return response()->json(['message' => 'User unblocked successfully'], 200);
    }
}
