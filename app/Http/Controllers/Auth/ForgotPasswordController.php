<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ForgotPasswordController extends Controller
{

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent to your email'], 200)
            : response()->json(['message' => 'Unable to send reset link'], 400);
    }


    public function sendOtp(Request $request)
    {
        // Generate a random OTP
        $otp = rand(1000, 9999);
        // dd( $otp );
        // Store the OTP in the cache for later verification
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(10));

        // Send the OTP via email
        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request)
    {
        $cachedOtp = Cache::get('otp_' . $request->email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            // OTP is valid, allow the user to reset their password
            Cache::forget('otp_' . $request->email);
            return response()->json(['message' => 'OTP verified successfully']);
        } else {
            return response()->json(['message' => 'Invalid OTP'], 401);
        }
    }
    
    public function resetPassword(Request $request)
    {
        $cachedOtp = Cache::get('otp_' . $request->email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            // OTP is valid, allow the user to reset their password
            Cache::forget('otp_' . $request->email);

            // Reset the user's password
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => bcrypt($request->new_password),
            ]);

            return response()->json(['message' => 'Password reset successfully']);
        } else {
            return response()->json(['message' => 'Invalid OTP'], 401);
        }
    }
}
