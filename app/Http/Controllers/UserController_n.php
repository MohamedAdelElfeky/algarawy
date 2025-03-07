<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Domain\Services\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(UserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());
        return response()->json($user->toArray(), 201);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $updatedUser = $this->userService->updateUser($user, $request->validated());
        return response()->json($updatedUser->toArray(), 200);
    }

    public function show(User $user)
    {
        $user->load('userDetails');
        return response()->json([
            'user' => $user,
        ]);
    }
}
