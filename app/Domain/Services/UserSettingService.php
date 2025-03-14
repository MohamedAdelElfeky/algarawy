<?php

namespace App\Domain\Services;

use App\Domain\Models\Setting;
use Illuminate\Support\Facades\Auth;

class UserSettingService
{
    public function toggleSetting(string $settingKey, mixed $value)
    {
        $user = Auth::user();
        $setting = Setting::where('key', $settingKey)->first();
        if (!$setting) {
            return ['success' => false, 'message' => "الإعداد {$settingKey} غير موجود."];
        }

        $userSetting = $user->userSettings()->where('setting_id', $setting->id)->first();
        $newValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($userSetting) {
            $userSetting->update(['value' => $newValue]);
        } else {
            $user->userSettings()->create([
                'setting_id' => $setting->id,
                'value' => $newValue,
            ]);
        }

        return ['success' => true, 'message' => 'تم تحديث الإعداد بنجاح.', 'value' => $newValue];
    }
}
