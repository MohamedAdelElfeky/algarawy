<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Region;
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
            'national_id' => 'required|string|size:11|unique:users',
            'password' => 'required|string|min:6',
            'birth_date' => 'required|date',
            'avatar' => 'nullable|string',
            'card_images' => 'nullable|array',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'national_card_image_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'national_card_image_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.required' => trans('validation.email.required'),
            'email.email' => trans('validation.email.email'),
            'email.unique' => trans('validation.email.unique'),
            'phone.required' => trans('validation.phone.required'),
            'phone.string' => trans('validation.phone.string'),
            'phone.unique' => trans('validation.phone.unique'),
            'national_id.required' => trans('validation.national_id.required'),
            'national_id.string' => trans('validation.national_id.string'),
            'national_id.size' => trans('validation.national_id.size'),
            'national_id.unique' => trans('validation.national_id.unique'),
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $imagePathAvatar = "";
        if (request()->hasFile('avatar')) {
            $imageAvatar = request()->file('avatar');
            $file_name_avatar = time() . rand(0, 9999999999999) . '_avatar.' . $imageAvatar->getClientOriginalExtension();
            $imageAvatar->move(public_path('user/'), $file_name_avatar);
            $imagePathAvatar = "user/" . $file_name_avatar;
        }
        $imagePathFront = "";
        if (request()->hasFile('national_card_image_front')) {
            $imageFront = request()->file('national_card_image_front');
            $file_name_front = time() . rand(0, 9999999999999) . '_front.' . $imageFront->getClientOriginalExtension();
            $imageFront->move(public_path('user/'), $file_name_front);
            $imagePathFront = "user/" . $file_name_front;
        }
        $imagePathBack = "";
        if (request()->hasFile('national_card_image_back')) {
            $imageBack = request()->file('national_card_image_back');
            $file_name_back = time() . rand(0, 9999999999999) . '_back.' . $imageBack->getClientOriginalExtension();
            $imageBack->move(public_path('user/'), $file_name_back);
            $imagePathBack = "user/" . $file_name_back;
        }


        $region = Region::findOrFail($request->input('region_id'));
        $city = City::findOrFail($request->input('city_id'));
        $neighborhood = Neighborhood::findOrFail($request->input('neighborhood_id'));
        // Create the user
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'location' => $request->input('location'),
            'birth_date' => $request->input('birth_date'),
            'national_id' => $request->input('national_id'),
            'avatar' => $imagePathAvatar,
            'card_images' => $request->input('card_images'),
            'region_id' => $region->id,
            'city_id' => $city->id,
            'neighborhood_id' => $neighborhood->id,
            'national_card_image_front' => $imagePathFront,
            'national_card_image_back' => $imagePathBack,
        ]);

        return response()->json(['message' => 'تم التسجيل بنجاح'], 201);
    }


    public function login(Request $request)
    {

        $credentials = $request->validate([
            'national_id' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = User::where('national_id', $credentials['national_id'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'بيانات الاعتماد غير صالحة'], 401);
        }

        if ($user->registration_confirmed != 1) {
            return response()->json(['message' => 'لم يتم موافقة حتي الان '], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }
}
