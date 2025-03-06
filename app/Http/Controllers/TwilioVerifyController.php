<?php

namespace App\Http\Controllers;

use Twilio\Rest\Client;
use Illuminate\Http\Request;

class TwilioVerifyController extends Controller
{

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^\+\d{10,15}$/'
        ]);

        $phone = preg_replace('/\s+/', '', $request->phone);

        try {
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

            $twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
                ->verifications
                ->create($phone, "sms");
            
            return response()->json(['message' => 'OTP sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
            'otp' => 'required|numeric'
        ]);

        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

        try {
            $verificationCheck = $twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
                ->verificationChecks
                ->create([
                    'to' => $request->phone,
                    'code' => $request->otp
                ]);

            if ($verificationCheck->status === 'approved') {
                return response()->json(['message' => 'Phone number verified successfully.']);
            } else {
                return response()->json(['error' => 'Invalid OTP.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
