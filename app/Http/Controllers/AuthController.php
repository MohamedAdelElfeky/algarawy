<?php

namespace App\Http\Controllers;

use App\Domain\Models\Otp;
use App\Domain\Models\Setting;
use App\Domain\Models\UserDetail;
use App\Domain\Services\UserService;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\NewPasswordEmail;
use App\Models\User;
use App\Services\FileHandlerService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct(private UserService $userService, private FileHandlerService $fileHandler)
    {
        $this->middleware('auth:sanctum')->except('register', 'login', 'sendOTP', 'resetPassword');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'national_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $this->userService->loginUser($credentials);
        if (!$user) {
            return response()->json(['message' => 'بيانات تسجيل الدخول غير صحيحة'], 401);
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

    public function PasswordReset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $newPassword = Str::random(10);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        Mail::to($user->email)->send(new NewPasswordEmail($newPassword));

        return response()->json([
            'message' => 'Password reset successful. Check your email for the new password.',
        ], 200);
    }

    public function toggleShowNoComplaintedPosts()
    {
        $user = Auth::user();

        $setting = Setting::where('key', 'show_no_complaints_posts')->first();

        if (!$setting) {
            return response()->json(['success' => false, 'message' => 'Setting not found'], 404);
        }

        $userSetting = $user->userSettings()->where('setting_id', $setting->id)->first();
        if ($userSetting) {
            $newValue = !$userSetting->value;
            $userSetting->update(['value' => $newValue]);
        } else {
            $user->userSettings()->create([
                'setting_id' => $setting->id,
                'value' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Show No Complaints Posts setting toggled successfully.',
            'value' => isset($newValue) ? $newValue : true,
        ]);
    }


    public function register(UserRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $imageFields = ['avatar', 'national_card_image_front', 'national_card_image_back'];

            $user = User::updateOrCreate(
                ['email' => $validatedData['email']],
                [
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'email' => $validatedData['email'],
                    'phone' => $validatedData['phone'],
                    'password' => bcrypt($validatedData['password']),
                    'national_id' => $validatedData['national_id'],
                ]
            );

            $userDetail = UserDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'birthdate' => $validatedData['birth_date'] ?? null,
                    'region_id' => $validatedData['region_id'] ?? null,
                    'city_id' => $validatedData['city_id'] ?? null,
                    'neighborhood_id' => $validatedData['neighborhood_id'] ?? null,
                ]
            );

            foreach ($imageFields as $field) {
                $this->fileHandler->uploadSingleImage($request, $userDetail, 'users', 'user', 'image', $field);
            }

            return response()->json([
                'message' => 'تم التسجيل بنجاح',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء التسجيل', 'details' => $e->getMessage()], 500);
        }
    }
}
