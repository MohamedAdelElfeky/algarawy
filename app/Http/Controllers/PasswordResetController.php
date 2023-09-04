<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Services\OtpService;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    use ResetsPasswords;

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Show the OTP request form
    public function requestOtpForm()
    {
        return view('auth.verify-otp');
    }

    // Send OTP to the user's email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');
        $user = OtpService::generateAndSendOtp($email);

        return view('auth.verify-otp', compact('user'));
    }

    // Verify OTP and show password reset form
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'email' => 'required|email',
        ]);

        $otp = $request->input('otp');
        $email = $request->input('email');

        $otpModel = Otp::where('email', $email)
            ->where('otp', $otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpModel) {
            // OTP is valid
            // Implement your password update logic here
            // You can redirect the user to a password reset form
            return view('auth.reset-password', compact('otpModel'));
        } else {
            // OTP is invalid or expired
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }
    }

    // Reset the password based on OTP verification
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $otp = $request->input('otp');
        $email = $request->input('email');
        $password = bcrypt($request->input('password'));

        $otpModel = Otp::where('email', $email)
            ->where('otp', $otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpModel) {
            // OTP is valid
            // Update the user's password
            $user = User::where('email', $email)->first();
            $user->password = $password;
            $user->save();

            // Mark the OTP as used
            $otpModel->used = true;
            $otpModel->save();

            // Log the user in or redirect them to the login page
            // You can implement your own login logic here

            return redirect()->route('login')->with('status', 'Password reset successfully. You can now log in with your new password.');
        } else {
            // OTP is invalid or expired
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }
    }
}
