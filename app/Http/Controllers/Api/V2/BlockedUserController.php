<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Services\BlockedUserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\v2\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BlockedUserController extends Controller
{
    public function __construct(private BlockedUserService $blockedUserService)
    {
        $this->middleware('auth:sanctum');
    }

    public function getBlockedUsers(): JsonResponse
    {
        $blockedUsers = $this->blockedUserService->getBlockedUsers(Auth::user());
        return response()->json(['data' => UserResource::collection($blockedUsers)]);
    }
}
