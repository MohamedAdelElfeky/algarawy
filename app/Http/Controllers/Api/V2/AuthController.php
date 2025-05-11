<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Services\V2\AuthService;
use App\Domain\Services\V2\UserCheckService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckUserExistsRequest;
use App\Http\Requests\OtpVerifyRequest;
use App\Http\Requests\TempRegisterRequest;

class AuthController extends Controller
{
    public function __construct(
        private UserCheckService $userCheckService,
        private AuthService $authService
    ) {}

    public function checkUserExists(CheckUserExistsRequest $request)
    {
        $validatedData = $request->validated();

        return $this->successResponse([
            'email_exists' => isset($validatedData['email'])
                ? $this->userCheckService->checkEmailExists($validatedData['email'])
                : false,
            'phone_exists' => isset($validatedData['phone'])
                ? $this->userCheckService->checkPhoneExists($validatedData['phone'])
                : false,
        ], 'تم التحقق من وجود المستخدم بنجاح');
    }
    private function successResponse(array $data, string $message)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message
        ]);
    }
    public function tempRegister(TempRegisterRequest $request)
    {
        $pending = $this->authService->tempRegister($request->validated());

        return response()->json(['message' => 'تم إرسال رمز التحقق'], 201);
    }
    public function verifyOtp(OtpVerifyRequest $request)
    {
        $response = $this->authService->verifyOtp($request->validated(), $request);

        return response()->json($response, $response['status']);
    }
}
