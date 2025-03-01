<?php

namespace Database\Seeders;

use App\Domain\Models\Setting;
use Illuminate\Database\Seeder;

class UserSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'mobile_number_visibility', 'type' => 'User', 'value' => true],
            ['key' => 'email_visibility', 'type' => 'User', 'value' => true],
            ['key' => 'show_no_complaints_posts', 'type' => 'User', 'value' => true],
            ['key' => 'birthdate_visibility', 'type' => 'User', 'value' => true],
            ['key' => 'registration_confirmed', 'type' => 'User', 'value' => true],
        ];
        
        foreach ($settings as $setting) {
            Setting::updateOrInsert(
                ['key' => $setting['key'], 'type' => $setting['type']], 
                ['value' => $setting['value']]
            );
        }
        
    }
}
