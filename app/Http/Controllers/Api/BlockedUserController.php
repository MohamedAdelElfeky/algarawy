<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\BlockedUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlockedUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Toggle block/unblock user.
     */
    public function toggleBlock(Request $request)
    {
        $this->validateRequest($request);

        $user = Auth::id();
        $blockedUserId = $request->blocked_user_id;

        $existingRecord = BlockedUser::where(['user_id' => $user, 'blocked_user_id' => $blockedUserId])->first();

        if ($existingRecord) {
            $existingRecord->delete();
            return $this->responseMessage('تم إلغاء حظر المستخدم بنجاح', false, 200);
        }

        BlockedUser::create(['user_id' => $user, 'blocked_user_id' => $blockedUserId]);

        return $this->responseMessage('تم حظر المستخدم بنجاح', true, 201);
    }

    /**
     * Get all blocked users for authenticated user.
     */
    public function getBlockedUsers()
    {
        $user = Auth::user();
        $blockedUsers = $user->blockedUsers()->get();
        return response()->json(['data' => UserResource::collection($blockedUsers)]);
    }

    /**
     * Validate block user request.
     */
    private function validateRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blocked_user_id' => 'required|exists:users,id',
        ], [
            'blocked_user_id.required' => 'معرف المستخدم المحظور مطلوب.',
            'blocked_user_id.exists' => 'المستخدم المحدد غير موجود.',
        ]);

        if ($validator->fails()) {
            response()->json([
                'message' => 'خطأ في التحقق من البيانات',
                'error' => $validator->errors()->first(),
            ], 422)->send();
            exit;
        }
    }

    /**
     * Return a JSON response message.
     */
    private function responseMessage(string $message, bool $isBlocked, int $statusCode)
    {
        return response()->json([
            'message' => $message,
            'is_blocked' => $isBlocked,
        ], $statusCode);
    }
}
