<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $token)
    {
        // Extract token and email from the query parameters
        $email = $request->query('email');

        // Pass token and email to the view
        return view('pages.auth.reset-password', compact('token', 'email'));
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        //  dd($request->all());
        // Attempt to reset the user's password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));  // Optional event to notify user
            }
        );

        // Check the result of the reset operation
        if ($status == Password::PASSWORD_RESET) {
            // If password reset is successful, redirect to login
            return redirect()->route('login')->with('status', __('Your password has been reset successfully.'));
        }

        // If password reset fails, return back with input and error message
        return back()
            ->withInput($request->only('email'))  // Preserving the email field
            ->withErrors(['email' => __('The provided password reset token is invalid.')]);  // Show the error message
    }
}
