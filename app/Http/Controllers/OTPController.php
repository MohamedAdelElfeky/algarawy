<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Applications\UseCases\SendOTPUseCase;
use App\Applications\UseCases\VerifyOTPUseCase;
use App\Applications\DTOs\OTPVerificationDTO;

class OTPController extends Controller
{
    protected SendOTPUseCase $sendOTPUseCase;
    protected VerifyOTPUseCase $verifyOTPUseCase;

    public function __construct(SendOTPUseCase $sendOTPUseCase, VerifyOTPUseCase $verifyOTPUseCase)
    {
        $this->sendOTPUseCase = $sendOTPUseCase;
        $this->verifyOTPUseCase = $verifyOTPUseCase;
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|regex:/^\+\d{10,15}$/']);

        $this->sendOTPUseCase->execute($request->phone);

        return response()->json(['message' => 'OTP sent successfully.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['phone' => 'required', 'otp' => 'required']);

        $dto = new OTPVerificationDTO($request->phone, $request->otp);

        if ($this->verifyOTPUseCase->execute($dto)) {
            return response()->json(['message' => 'Phone verified successfully.']);
        }

        return response()->json(['error' => 'Invalid OTP.'], 400);
    }
}
