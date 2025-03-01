<?php

namespace App\Imports;

use App\Domain\Models\Setting;
use App\Domain\Models\UserDetail;
use App\Domain\Models\UserSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $user = User::firstOrCreate(
            [
                'email' => $row['email'],
                'phone' => $row['phone'],
                'national_id' => $row['national_id']
            ],
            [
                'first_name'       => $row['first_name'],
                'last_name'        => $row['last_name'],
                'password'         => Hash::make($row['password'])?? Hash::make('123456'),
                'email_verified_at' => now(),
                'remember_token'   => $row['remember_token'] ?? null,
            ]
        );


        // Assign Role
        $roleName = $row['role'] ?? 'user';
        $userRole = Role::firstOrCreate(['name' => $roleName]);
        $user->assignRole($userRole);

        // Create or update User Detail
        UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'birthdate'       => !empty($row['birth_date']) ? Carbon::createFromFormat('d-m-Y', $row['birth_date'])->format('Y-m-d') : null,
                'location'        => $row['location'] ?? null,
                'region_id'       => $row['region_id'] ?? null,
                'city_id'         => $row['city_id'] ?? null,
                'neighborhood_id' => $row['neighborhood_id'] ?? null,
            ]
        );

        // User Settings
        $settings = [
            'mobile_number_visibility' => true,
            'birthdate_visibility'     => true,
            'email_visibility'         => true,
            'show_no_complaints_posts' => true,
            'registration_confirmed'   => true,
        ];

        foreach ($settings as $settingName => $value) {
            UserSetting::updateOrCreate(
                ['user_id' => $user->id, 'setting_id' => $this->getSettingIdByName($settingName)],
                ['value' => $value]
            );
        }

        return $user;
    }

    private function getSettingIdByName($name)
    {
        return Setting::where('key', $name)->value('id');
    }
}
