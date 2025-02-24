<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            ['id' => 1, 'name' => 'الرياض'],
            ['id' => 2, 'name' => 'مكة المكرمة'],
            ['id' => 3, 'name' => 'الشرقية'],
            ['id' => 4, 'name' => 'المدينة المنورة'],
            ['id' => 5, 'name' => 'عسير'],
            ['id' => 6, 'name' => 'القصيم'],
            ['id' => 7, 'name' => 'تبوك'],
            ['id' => 8, 'name' => 'حائل'],
            ['id' => 9, 'name' => 'جازان'],
            ['id' => 10, 'name' => 'نجران'],
            ['id' => 11, 'name' => 'الباحة'],
            ['id' => 12, 'name' => 'الحدود الشمالية'],
            ['id' => 13, 'name' => 'الجوف'],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(['id' => $region['id']], ['name' => $region['name']]);
        }

    }
}
