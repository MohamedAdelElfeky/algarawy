<?php

namespace App\Http\Controllers;

use App\Domain\Models\Course;
use App\Domain\Models\Meeting;
use App\Domain\Models\Project;
use App\Domain\Models\Setting;
use App\Domain\Models\support;
use App\Domain\Models\UserDetail;
use App\Domain\Models\UserSetting;
use App\Domain\Services\PaginationService;
use App\Http\Resources\CourseResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FileHandlerService;
use App\Shared\Traits\HandlesSingleImageUpload;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function toggleUser($id)
    {
        $user = User::findOrFail($id);

        $setting = Setting::where('key', 'registration_confirmed')->first();

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

        return response()->json(['success' => true]);
    }

    public function makeAdmin($userId)
    {
        $user = User::findOrFail($userId);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole($adminRole);

        return redirect()->back()->with('success', 'User has been assigned as Admin.');
    }
}
