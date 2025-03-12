<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Entities\OTP;
use App\Domain\Entities\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Infrastructure\Services\TwilioService;
use App\Models\User;

class OtpController extends Controller
{
    public function __construct(private TwilioService $twilioService) {}


    public function sendOtp(Request $request): JsonResponse
    {
        $validator = $this->validatePhone($request);
        if ($validator->fails()) {
            return $this->validationError('رقم الهاتف غير صالح.');
        }

        try {
            $user = $this->findUserByPhone($request->phone);
            if (!$user) {
                return $this->notFoundError('رقم الهاتف غير مسجل.');
            }

            $phone = new PhoneNumber($request->phone);
            $this->twilioService->sendOtp($phone);

            return response()->json(['message' => 'تم إرسال رمز OTP بنجاح.'], 201);
        } catch (\Exception $e) {
            return $this->serverError($e);
        }
    }


    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = $this->validateOtpRequest($request);
        if ($validator->fails()) {
            return $this->validationError('التحقق من البيانات المدخلة فشل.');
        }

        try {
            $user = $this->findUserByPhone($request->phone);
            if (!$user) {
                return $this->notFoundError('رقم الهاتف غير مسجل.');
            }

            $phone = new PhoneNumber($request->phone);
            $otp = new OTP($request->otp);

            if ($this->twilioService->verifyOtp($phone, $otp)) {
                $user->update(['password' => bcrypt($request->input('password'))]);
                return response()->json(['message' => 'تم تغيير الرقم السري بنجاح.'], 200);
            }

            return response()->json(['message' => 'OTP غير صالح.'], 400);
        } catch (\Exception $e) {
            return $this->serverError($e);
        }
    }


    public function sendMessage(Request $request): JsonResponse
    {
        $validator = $this->validateMessageRequest($request);
        if ($validator->fails()) {
            return $this->validationError('البيانات غير صالحة.');
        }

        try {
            $user = $this->findUserByPhone($request->phone);
            if (!$user) {
                return $this->notFoundError('رقم الهاتف غير مسجل.');
            }

            $phone = new PhoneNumber($request->phone);
            $this->twilioService->sendMessage($phone, $request->message);

            return response()->json(['message' => 'تم إرسال الرسالة بنجاح.'], 201);
        } catch (\Exception $e) {
            return $this->serverError($e);
        }
    }


    private function validatePhone(Request $request)
    {
        return Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+\d{2}\d{8,15}$/',
        ]);
    }

    private function validateOtpRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+\d{2}\d{8,15}$/',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }


    private function validateMessageRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+\d{2}\d{9}$/',
            'message' => 'required|string|max:255',
        ]);
    }

    private function findUserByPhone(string $phone): ?User
    {
        $localPhone = $this->stripCountryCode($phone);
        return User::whereRaw("REPLACE(phone, ' ', '') = ?", [$localPhone])->first();
    }


    private function stripCountryCode(string $phone): string
    {
        return preg_replace('/^\+\d{1,3}/', '', $phone);
    }

    private function validationError(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 422);
    }

    private function notFoundError(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 404);
    }

    function serverError(\Exception $e): JsonResponse
    {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
