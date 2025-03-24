<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\UserDataService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(private UserDataService $userService)
    {
        $this->middleware('auth:sanctum');
    }

    public function getMeetings()
    {
        return response()->json([
            'meetings' => $this->userService->getMeetings(Auth::id()),
        ], 200);
    }

    public function getCourses()
    {
        return response()->json([
            'courses' => $this->userService->getCourses(Auth::id()),
        ], 200);
    }

    public function getUser()
    {
        return response()->json([
            'user' => new UserResource(Auth::user()->load('details.region', 'details.city', 'details.neighborhood', 'details.images')),
        ], 200);
    }

    public function getDataUser($userId)
    {
        return response()->json($this->userService->getUserData($userId));
    }

    public function updateProfile(UpdateProfileRequest $request, User $user)
    {
        return response()->json(new UserResource($this->userService->updateProfile($request, $user)));
    }

    public function getNotificationsForUser(Request $request)
    {
        $perPage = $request->header('per_page');
        $page = $request->header('page');

        $response = $this->userService->getNotificationsForUser($perPage, $page);

        return response()->json($response);
    }

    public function changePassword(Request $request)
    {
        // dd($request->all());
        $response = $this->userService->changePassword($request->all());

        return response()->json($response, $response['status']);
    }
}
