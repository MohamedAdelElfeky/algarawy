<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'location' => 'required|string',
            'birthdate' => 'required|date',
            'national_id' => 'required|string',
            'card_images' => 'nullable|json',
            'avatar' => 'nullable|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'location' => $request->input('location'),
            'birthdate' => $request->input('birthdate'),
            'national_id' => $request->input('national_id'),
            'card_images' => $request->input('card_images'),
            'avatar' => $request->input('avatar'),
        ]);

        // $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Registration successful'], 200);
    }


    public function login(Request $request)
    {
        // Validate the incoming request data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Retrieve the authenticated user
        $user = $request->user();

        // Generate a new API token for the user
        $token = $user->createToken('authToken')->plainTextToken;

        // Return the user and token information
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}
