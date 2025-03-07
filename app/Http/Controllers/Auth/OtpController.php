<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Entities\OTP;
use App\Domain\Entities\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Infrastructure\Services\TwilioService;
use App\Models\User;

class OtpController extends Controller
{
    protected TwilioService $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    // Send OTP via Twilio
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+\d{2}\d{8,15}$/',
        ]);
        // 'phone' => 'required|regex:/^\+\d{1,3}\d{8,15}$/',

        if ($validator->fails()) {
            return response()->json(['message' => 'رقم الهاتف غير صالح.'], 422);
        }

        try {
            $localPhone = $this->stripCountryCode($request->phone);
            $user = User::whereRaw("REPLACE(phone, ' ', '') = ?", [$localPhone])->first();
            // dd($localPhone);

            if (!$user) {
                return response()->json(['message' => 'رقم الهاتف غير مسجل.'], 404);
            }
            $phone = new PhoneNumber($request->phone);
            $this->twilioService->sendOtp($phone);
            return response()->json(['message' => 'تم إرسال رمز OTP بنجاح.'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+\d{2}\d{9}$/',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'التحقق من البيانات المدخلة فشل.'], 422);
        }

        try {
            $localPhone = $this->stripCountryCode($request->phone);
            $user = User::whereRaw("REPLACE(phone, ' ', '') = ?", [$localPhone])->first();
            if (!$user) {
                return response()->json(['message' => 'رقم الهاتف غير مسجل.'], 404);
            }
            $phone = new PhoneNumber($request->phone);
            $otp = new OTP($request->otp);
            $isVerified = $this->twilioService->verifyOtp($phone, $otp);
            if ($isVerified) {
                $user = User::where('phone', $phone)->first();
                $user->update(['password' => bcrypt($request->input('password'))]);
                return response()->json(['message' => 'تم تغيير الرقم السري بنجاح.'], 200);
            } else {
                return response()->json(['message' => 'OTP غير صالح.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Send a custom SMS to a user
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+\d{2}\d{9}$/',
            'message' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'البيانات غير صالحة.'], 422);
        }

        try {
            $localPhone = $this->stripCountryCode($request->phone);
            $user = User::whereRaw("REPLACE(phone, ' ', '') = ?", [$localPhone])->first();
            if (!$user) {
                return response()->json(['message' => 'رقم الهاتف غير مسجل.'], 404);
            }
            $phone = new PhoneNumber($request->phone);
            $message = $request->message;

            $this->twilioService->sendMessage($phone, $message);

            return response()->json(['message' => 'تم إرسال الرسالة بنجاح.'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function stripCountryCode($phone)
    {
        return preg_replace('/^\+\d{1,3}/', '', $phone);
    }
}
