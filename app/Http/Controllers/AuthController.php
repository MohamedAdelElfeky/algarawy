<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'location' => 'required|string',
            'birth_date' => 'required|date',
            'national_id' => 'required|string|unique:users',
            'avatar' => 'nullable|string',
            'card_images' => 'nullable|array',
            'governorate' => 'required|string',
            'city' => 'required|string',
            'area' => 'required|string',
            'national_card_image_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'national_card_image_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle the file uploads and save the file paths
        // $frontImage = $request->file('national_card_image_front')->store('public/images/frontImage');
        // $file = request()->file('path');
        // $file_name = time() . rand(0, 9999999999999) . '_image.' . $file->getClientOriginalExtension();
        // $file->move(public_path('images/category'), $file_name);
        // $backImage = $request->file('national_card_image_back')->store('public/images/backImage');

        $frontImage = $request->file('national_card_image_front');
        $backImage = $request->file('national_card_image_back');

        $frontImagePath = null;
        $backImagePath = null;

        if ($frontImage) {
            $frontImagePath = $frontImage->store('public/images/frontImage');
        }

        if ($backImage) {
            $backImagePath = $backImage->store('public/images/backImage');
        }

        // Create the user
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'name' => $request->input('first_name') . ' ' . $request->input('last_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'location' => $request->input('location'),
            'birth_date' => $request->input('birth_date'),
            'national_id' => $request->input('national_id'),
            'avatar' => $request->input('avatar'),
            'card_images' => $request->input('card_images'),
            'governorate' => $request->input('governorate'),
            'city' => $request->input('city'),
            'area' => $request->input('area'),
            'national_card_image_front' => $frontImagePath,
            'national_card_image_back' => $backImagePath,
        ]);

        return response()->json(['message' => 'Registration successful'], 200);
    }


    public function login(Request $request)
    {
        // Validate the incoming request data
        $credentials = $request->validate([
            'national_id' => 'required|string',
            'password' => 'required|string',
        ]);
        // Fetch the user by 'national_id'
        $user = User::where('national_id', $credentials['national_id'])->first();
        // Check if the user exists and the provided password is correct
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        // Generate a new API token for the user
        $token = $user->createToken('authToken')->plainTextToken;

        // Return the user and token information
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}
