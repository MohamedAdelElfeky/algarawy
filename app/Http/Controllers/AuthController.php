<?php

namespace App\Http\Controllers;

use App\Domain\Models\UserDevice;
use App\Domain\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct(private AuthService $authService)
    {
        $this->middleware('auth:sanctum')->except('register', 'login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $response = $this->authService->login($credentials, $request);
        return response()->json($response, $response['status']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        UserDevice::where('device_id', $request->device_id)->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function register(UserRequest $request)
    {
        $response = $this->authService->register($request->validated(), $request);
        return response()->json($response, $response['status']);
    }
}
