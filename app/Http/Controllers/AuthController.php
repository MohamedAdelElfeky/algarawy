<?php

namespace App\Http\Controllers;

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
            'password' => 'required|string|min:6',
            'birth_date' => 'required|date',
            'national_id' => 'required|string|size:11|unique:users',
            'avatar' => 'nullable|string',
            'card_images' => 'nullable|array',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'national_card_image_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'national_card_image_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'name' => $request->input('first_name') . ' ' . $request->input('last_name'),
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

        return response()->json(['message' => 'تم التسجيل بنجاح'], 200);
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
            return response()->json(['message' => 'بيانات الاعتماد غير صالحة'], 401);
        }
        // Generate a new API token for the user
        $token = $user->createToken('authToken')->plainTextToken;

        // Return the user and token information
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function update(Request $request, User $user)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6', // Password is optional for update
            'birth_date' => 'nullable|date',
            'national_id' => 'nullable|string|size:11|unique:users,national_id,' . $user->id,
            'avatar' => 'nullable|string',
            'card_images' => 'nullable|array',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'national_card_image_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'national_card_image_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update user profile data
        $user->fill($request->except('password'));

        // Handle password update if provided
        if ($request->has('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        // Handle file uploads
        if ($request->hasFile('national_card_image_front')) {
            $file = $request->file('national_card_image_front');
            $fileName = time() . '_front.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $user->national_card_image_front = $filePath;
        }

        if ($request->hasFile('national_card_image_back')) {
            $file = $request->file('national_card_image_back');
            $fileName = time() . '_back.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $user->national_card_image_back = $filePath;
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function searchUser(Request $request)
    {
        $searchTerm = $request->input('search');
        $region_id = $request->input('region_id');
        $city_id = $request->input('city_id');
        $neighborhood_id = $request->input('neighborhood_id');

        $users = User::where(function ($query) use ($searchTerm) {
            $fields = ['first_name', 'last_name', 'name', 'phone'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })
            ->when($region_id, function ($query) use ($region_id) {
                return $query->orWhere('region_id', $region_id);
            })
            ->when($city_id, function ($query) use ($city_id) {
                return $query->orWhere('city_id', $city_id);
            })
            ->when($neighborhood_id, function ($query) use ($neighborhood_id) {
                return $query->orWhere('neighborhood_id', $neighborhood_id);
            })
            ->get();

        return response()->json($users);
    }
}
