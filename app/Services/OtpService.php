<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class OtpService
{
    public static function generateAndSendOtp($email)
    {
        $otp = rand(100000, 999999); // Generate a 6-digit OTP
        $expiresAt = Carbon::now()->addMinutes(15); // OTP expires in 15 minutes

        $otpModel = Otp::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ]);

        Mail::to($email)->send(new OtpMail($otp)); // Send OTP via email

        return $otpModel;
    }
}
