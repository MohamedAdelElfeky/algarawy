<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\BlockedUserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlockUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BlockedUserController extends Controller
{
    public function __construct(private BlockedUserService $blockedUserService)
    {
        $this->middleware('auth:sanctum');
    }

    public function toggleBlock(BlockUserRequest $request): JsonResponse
    {
        $isBlocked = $this->blockedUserService->toggleBlock(Auth::id(), $request->blocked_user_id);
        return $this->responseMessage(
            $isBlocked ? 'تم حظر المستخدم بنجاح' : 'تم إلغاء حظر المستخدم بنجاح',
            $isBlocked,
            $isBlocked ? 201 : 200
        );
    }

    public function getBlockedUsers(): JsonResponse
    {
        $blockedUsers = $this->blockedUserService->getBlockedUsers(Auth::user());
        return response()->json(['data' => UserResource::collection($blockedUsers)]);
    }

    private function responseMessage(string $message, bool $isBlocked, int $statusCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'is_blocked' => $isBlocked,
        ], $statusCode);
    }
}
