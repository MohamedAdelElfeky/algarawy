<?php

namespace App\Domain\Services;

use App\Models\User;
use App\Domain\Models\UserDetail;
use App\Domain\Models\UserSetting;
use App\Domain\Repositories\UserWebRepository;
use App\Shared\Traits\HandlesSingleImageUpload;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserWebService
{
    use HandlesSingleImageUpload;

    public function __construct(private UserWebRepository $userRepository) {}

    public function getActiveUsers()
    {
        return $this->userRepository->getUsersBySetting('registration_confirmed', 1);
    }

    public function getInactiveUsers()
    {
        return $this->userRepository->getUsersBySetting('registration_confirmed', 0);
    }

    public function getAdmins()
    {
        return $this->userRepository->getUsersByRole('admin');
    }

    public function createUser(array $data, Request $request)
    {
        $user = User::updateOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'national_id' => $data['national_id'],
            ]
        );

        $userDetail = UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            ['birthdate' => $data['birth_date']]
        );

        $imageFields = ['avatar', 'national_card_image_front', 'national_card_image_back'];
        foreach ($imageFields as $field) {
            $this->uploadSingleImage($request, $userDetail, 'users', 'user', $field, $field);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole($adminRole);

        $settings = [
            'mobile_number_visibility' => true,
            'birthdate_visibility' => true,
            'email_visibility' => true,
            'registration_confirmed' => true,
            'show_no_complaints_posts' => true,
        ];

        foreach ($settings as $key => $value) {
            UserSetting::updateOrCreate(
                ['user_id' => $user->id, 'setting_id' => $this->getSettingIdByName($key)],
                ['value' => $value]
            );
        }

        return $user;
    }

    private function getSettingIdByName($name)
    {
        return \App\Domain\Models\Setting::where('key', $name)->value('id');
    }

    public function updateUserPassword(int $userId, string $newPassword)
    {
        $user = $this->userRepository->findUserById($userId);
        if (!$user) {
            return false;
        }

        $user->password = Hash::make($newPassword);
        $user->save();
        return true;
    }
    
}
