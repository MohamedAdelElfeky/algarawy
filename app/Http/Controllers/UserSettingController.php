<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Domain\Services\UserSettingService;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{
    
    public function __construct(private UserSettingService $userSettingService)
    {
        $this->middleware('auth:sanctum');
    }

    public function toggleShowNoComplaintedPosts()
{
    $user = Auth::user();

    $currentValue = $user->userSettings()
        ->whereHas('setting', fn($query) => $query->where('key', 'show_no_complaints_posts'))
        ->value('value') ?? false;

    $newValue = !$currentValue;

    $response = $this->userSettingService->toggleSetting('show_no_complaints_posts', $newValue);

    $status = $response['success'] ? 200 : 400;

    return response()->json($response, $status);
}


    public function toggleVisibility(Request $request)
    {
        $fields = ['mobile_number', 'birthdate', 'email'];
        $errors = [];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $response = $this->userSettingService->toggleSetting("{$field}_visibility", $request->input($field));

                if (!$response['success']) {
                    $errors[] = $response['message'];
                }
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 400);
        }

        return response()->json(['message' => 'تم تحديث إعدادات الرؤية بنجاح.']);
    }
}
