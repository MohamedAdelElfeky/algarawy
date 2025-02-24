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
        $cities = [
            // الرياض
            ['id' => 1, 'name' => 'الرياض', 'region_id' => 1],
            ['id' => 2, 'name' => 'الخرج', 'region_id' => 1],
            ['id' => 3, 'name' => 'الدوادمي', 'region_id' => 1],
            // مكة المكرمة
            ['id' => 4, 'name' => 'جدة', 'region_id' => 2],
            ['id' => 5, 'name' => 'مكة المكرمة', 'region_id' => 2],
            ['id' => 6, 'name' => 'الطائف', 'region_id' => 2],
            // الشرقية
            ['id' => 7, 'name' => 'الدمام', 'region_id' => 3],
            ['id' => 8, 'name' => 'الخبر', 'region_id' => 3],
            ['id' => 9, 'name' => 'الأحساء', 'region_id' => 3],
            // المدينة المنورة
            ['id' => 10, 'name' => 'المدينة المنورة', 'region_id' => 4],
            ['id' => 11, 'name' => 'ينبع', 'region_id' => 4],
            // عسير
            ['id' => 12, 'name' => 'أبها', 'region_id' => 5],
            ['id' => 13, 'name' => 'خميس مشيط', 'region_id' => 5],
            // القصيم
            ['id' => 14, 'name' => 'بريدة', 'region_id' => 6],
            ['id' => 15, 'name' => 'عنيزة', 'region_id' => 6],
            // تبوك
            ['id' => 16, 'name' => 'تبوك', 'region_id' => 7],
            // حائل
            ['id' => 17, 'name' => 'حائل', 'region_id' => 8],
            // جازان
            ['id' => 18, 'name' => 'جازان', 'region_id' => 9],
            // نجران
            ['id' => 19, 'name' => 'نجران', 'region_id' => 10],
            // الباحة
            ['id' => 20, 'name' => 'الباحة', 'region_id' => 11],
            // الحدود الشمالية
            ['id' => 21, 'name' => 'عرعر', 'region_id' => 12],
            // الجوف
            ['id' => 22, 'name' => 'سكاكا', 'region_id' => 13],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(['id' => $city['id']], [
                'name' => $city['name'],
                'region_id' => $city['region_id'],
            ]);
        }

    }
}
