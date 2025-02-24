<?php

namespace Database\Seeders;

use App\Domain\Models\Setting;
use App\Domain\Models\User;
use App\Domain\Models\UserDetail;
use App\Domain\Models\UserSetting;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        $adminUser = User::create([
            'first_name'              => 'super',
            'last_name'              => 'admin',
            'email'             => 'admin@admin.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
            'phone'             => $faker->phoneNumber,
            'national_id'       => '01234567',
        ]);
        UserDetail::create([
            'user_id' => $adminUser->id,
            'birthdate' => '1990-01-01',
            'region_id' => 1,
            'city_id' => 1,
            'neighborhood_id' => 1,
        ]);
        $settings = Setting::getSettingsByType('User');
        foreach ($settings as $setting) {
            UserSetting::create([
                'user_id' => $adminUser->id,
                'setting_id' => $setting->id,
                'value' => true
            ]);
        }
        $demoUser = User::create([
            'first_name'              => 'user',
            'last_name'              => 'name',
            'email'             => 'user@user.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
            'phone'             => $faker->phoneNumber,
            'national_id'       => '12345678',
        ]);
        UserDetail::create([
            'user_id' => $demoUser->id,
            'birthdate' => '1990-01-01',
            'region_id' => 1,
            'city_id' => 1,
            'neighborhood_id' => 1,
        ]);
    }
}
