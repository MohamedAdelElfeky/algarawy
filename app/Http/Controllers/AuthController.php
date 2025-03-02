<?php

namespace App\Http\Controllers;

use App\Domain\Models\Setting;
use App\Domain\Services\UserService;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\NewPasswordEmail;
use App\Mail\ResetPasswordMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth:sanctum')->except('register', 'login');
        $this->userService = $userService;
    }

    public function register(UserRequest $request)
    {
        $user = $this->userService->registerUser($request->validated());

        return response()->json([
            'message' => 'تم التسجيل بنجاح',
            'user' => $user
        ], 201);
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
        //        dd($token);
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }
    // public function register(Request $request)
    // {
    //     // Validate the incoming request data
    //     $validator = Validator::make($request->all(), [
    //         'first_name' => 'required|string',
    //         'last_name' => 'required|string',
    //         'email' => 'required|email|unique:users',
    //         'phone' => 'required|string|unique:users',
    //         'national_id' => 'required|string|size:10|unique:users',
    //         'password' => 'required|string|min:6',
    //         'birth_date' => 'required|date',
    //         'avatar' => 'nullable|string',
    //         'card_images' => 'nullable|array',
    //         'region_id' => 'nullable|exists:regions,id',
    //         'city_id' => 'nullable|exists:cities,id',
    //         'neighborhood_id' => 'nullable|exists:neighborhoods,id',
    //         'national_card_image_front' => 'nullable|image|mimes:jpeg,png,jpg,gif',
    //         'national_card_image_back' => 'nullable|image|mimes:jpeg,png,jpg,gif',
    //     ], [
    //         'email.required' => trans('validation.email.required'),
    //         'email.email' => trans('validation.email.email'),
    //         'email.unique' => trans('validation.email.unique'),
    //         'phone.required' => trans('validation.phone.required'),
    //         'phone.string' => trans('validation.phone.string'),
    //         'phone.unique' => trans('validation.phone.unique'),
    //         'national_id.required' => trans('validation.national_id.required'),
    //         'national_id.string' => trans('validation.national_id.string'),
    //         'national_id.size' => trans('validation.national_id.size'),
    //         'national_id.unique' => trans('validation.national_id.unique'),
    //     ]);

    //     // Check if validation fails
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }
    //     $imagePathAvatar = "";
    //     if (request()->hasFile('avatar')) {
    //         $imageAvatar = request()->file('avatar');
    //         $file_name_avatar = time() . rand(0, 9999999999999) . '_avatar.' . $imageAvatar->getClientOriginalExtension();
    //         $imageAvatar->move(public_path('user/'), $file_name_avatar);
    //         $imagePathAvatar = "user/" . $file_name_avatar;
    //     }
    //     $imagePathFront = "";
    //     if (request()->hasFile('national_card_image_front')) {
    //         $imageFront = request()->file('national_card_image_front');
    //         $file_name_front = time() . rand(0, 9999999999999) . '_front.' . $imageFront->getClientOriginalExtension();
    //         $imageFront->move(public_path('user/'), $file_name_front);
    //         $imagePathFront = "user/" . $file_name_front;
    //     }
    //     $imagePathBack = "";
    //     if (request()->hasFile('national_card_image_back')) {
    //         $imageBack = request()->file('national_card_image_back');
    //         $file_name_back = time() . rand(0, 9999999999999) . '_back.' . $imageBack->getClientOriginalExtension();
    //         $imageBack->move(public_path('user/'), $file_name_back);
    //         $imagePathBack = "user/" . $file_name_back;
    //     }

    //     // Create the user
    //     $user = User::create([
    //         'first_name' => $request->input('first_name'),
    //         'last_name' => $request->input('last_name'),
    //         'email' => $request->input('email'),
    //         'phone' => $request->input('phone'),
    //         'password' => bcrypt($request->input('password')),
    //         'location' => $request->input('location'),
    //         'birth_date' => $request->input('birth_date'),
    //         'national_id' => $request->input('national_id'),
    //         'avatar' => $imagePathAvatar,
    //         'card_images' => $request->input('card_images'),
    //         'region_id' => $request->input('region_id'),
    //         'city_id' => $request->input('city_id'),
    //         'neighborhood_id' =>$request->input('neighborhood_id'),
    //         'national_card_image_front' => $imagePathFront,
    //         'national_card_image_back' => $imagePathBack,
    //     ]);

    //     return response()->json(['message' => 'تم التسجيل بنجاح'], 201);
    // }


    // public function login(Request $request)
    // {

    //     $credentials = $request->validate([
    //         'national_id' => 'required|string',
    //         'password' => 'required|string',
    //     ]);
    //     $user = User::where('national_id', $credentials['national_id'])->first();

    //     if (!$user || !Hash::check($credentials['password'], $user->password)) {
    //         return response()->json(['message' => 'بيانات الاعتماد غير صالحة'], 401);
    //     }

    //     if ($user->registration_confirmed != 1) {
    //         return response()->json(['message' => 'لم يتم موافقة حتي الان '], 401);
    //     }

    //     $token = $user->createToken('authToken')->plainTextToken;
    //     return response()->json([
    //         'user' => new UserResource($user),
    //         'token' => $token,
    //     ], 201);
    // }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function PasswordReset(Request $request)
    {
        dd($request);
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $newPassword = Str::random(10); // Generate a random password

        $user->update([
            'password' => Hash::make($newPassword), // Update the user's password
        ]);

        // Send the new password to the user's email
        Mail::to($user->email)->send(new NewPasswordEmail($newPassword));

        return response()->json([
            'message' => 'Password reset successful. Check your email for the new password.',
        ], 200);
    }

    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'عنوان البريد الإلكتروني غير صالح.'], 422);
        }

        $email = $request->input('email');
        $otp = rand(1000, 9999);

        Otp::updateOrCreate(
            ['email' => $email],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(10), 'used' => false]
        );

        Mail::to($email)->send(new ResetPasswordMail($otp));

        return response()->json(['message' => 'تم إرسال كلمة المرور لمرة واحدة (OTP) بنجاح.'], 201);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطئ في التحقق.'], 422);
        }

        $email = $request->input('email');
        $otp = $request->input('otp');
        $password = $request->input('password');

        $reset = Otp::where('email', $email)
            ->where('otp', $otp)
            ->where('used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'كلمة المرور لمرة واحدة غير صالحة.'], 422);
        }

        // Update the user's password
        $user = User::where('email', $email)->first();
        $user->update(['password' => bcrypt($password)]);

        // Mark the OTP as used
        $reset->update(['used' => true]);

        return response()->json(['message' => 'تم تغيير الرقم السري بنجاح.'], 201);
    }

    public function toggleShowNoComplaintedPosts()
    {
        $user = Auth::user();

        $setting = Setting::where('key', 'show_no_complaints_posts')->first();

        if (!$setting) {
            return response()->json(['success' => false, 'message' => 'Setting not found'], 404);
        }

        $userSetting = $user->userSettings()->where('setting_id', $setting->id)->first();
        // dd($userSetting);
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
}
