<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::create(['name' => 'الهفوف', 'region_id' => 1]);
        City::create(['name' => 'ضياء', 'region_id' => 2]);
        City::create(['name' => 'الدرعية', 'region_id' =>  3]);
        City::create(['name' => 'الطائف', 'region_id' =>  4]);
        City::create(['name' => 'الجييل', 'region_id' =>  1]);
        City::create(['name' => 'مكة', 'region_id' => 2]);
        City::create(['name' => 'تبوك', 'region_id' =>  3]);
        City::create(['name' => 'الدمام', 'region_id' =>  1]);
        City::create(['name' => 'جدة', 'region_id' =>  1]);
        City::create(['name' => 'المدينة المنورة', 'region_id' =>  2]);
        City::create(['name' => 'أخري', 'region_id' =>  null]);

    }
}
