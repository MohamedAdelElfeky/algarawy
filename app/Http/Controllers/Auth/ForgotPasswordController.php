<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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


    // public function sendOtp(Request $request)
    // {
    //     // Generate a random OTP
    //     $otp = rand(1000, 9999);
    //     // dd( $otp );
    //     // Store the OTP in the cache for later verification
    //     Cache::put('otp_' . $request->email, $otp, now()->addMinutes(10));

    //     // Send the OTP via email
    //     Mail::to($request->email)->send(new SendOtpMail($otp));

    //     return response()->json(['message' => 'OTP sent successfully']);
    // }

    // public function verifyOtp(Request $request)
    // {
    //     $cachedOtp = Cache::get('otp_' . $request->email);

    //     if ($cachedOtp && $cachedOtp == $request->otp) {
    //         // OTP is valid, allow the user to reset their password
    //         Cache::forget('otp_' . $request->email);
    //         return response()->json(['message' => 'OTP verified successfully']);
    //     } else {
    //         return response()->json(['message' => 'Invalid OTP'], 401);
    //     }
    // }


    public function sendOtp(Request $request)
    {
        // Generate a random OTP
        $otp = rand(1000, 9999);

        // Store the OTP in the database
        Otp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(3),
            'used' => false,
        ]);
        // Send the OTP via email
        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request)
    {
        // Find the OTP record
        $otpRecord = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>=', Carbon::now())
            ->first();

        if ($otpRecord) {
            // Mark OTP as used
            $otpRecord->update(['used' => true]);

            return response()->json(['message' => 'OTP verified successfully']);
        } else {
            return response()->json(['message' => 'Invalid OTP'], 401);
        }
    }

    public function resetPassword(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:4',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Find the OTP record
        $otpRecord = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if ($otpRecord) {
            // Mark OTP as used
            $otpRecord->update(['used' => true]);

            // Find the user by email and update their password
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update([
                    'password' => bcrypt($request->new_password),
                ]);

                return response()->json(['message' => 'Password reset successfully']);
            } else {
                return response()->json(['message' => 'User not found'], 404);
            }
        } else {
            return response()->json(['message' => 'Invalid OTP'], 401);
        }
    }
}
