<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Region;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // RegionSeeder::class,
            // CitySeeder::class,
            // NeighborhoodSeeder::class,
            // UsersSeeder::class,
        ]);
        // Regions
        //     $regions = [
        //         ['id' => 1, 'name' => 'الجنوبية'],
        //         ['id' => 2, 'name' => 'الشرقية'],
        //         ['id' => 3, 'name' => 'الشمالية'],
        //         ['id' => 4, 'name' => 'الغربية'],
        //         ['id' => 5, 'name' => 'أخري'],
        //     ];

        //     foreach ($regions as $region) {
        //         Region::updateOrCreate(['id' => $region['id']], ['name' => $region['name']]);
        //     }

        //     // Cities
        //     $cities = [
        //         ['id' => 1, 'name' => 'الهفوف', 'region_id' => 1],
        //         ['id' => 2, 'name' => 'ضياء', 'region_id' => 2],
        //         ['id' => 3, 'name' => 'الدرعية', 'region_id' => 3],
        //         ['id' => 4, 'name' => 'الطائف', 'region_id' => 4],
        //         ['id' => 5, 'name' => 'الجييل', 'region_id' => 1],
        //         ['id' => 6, 'name' => 'مكة', 'region_id' => 2],
        //         ['id' => 7, 'name' => 'تبوك', 'region_id' => 3],
        //         ['id' => 8, 'name' => 'الدمام', 'region_id' => 1],
        //         ['id' => 9, 'name' => 'جدة', 'region_id' => 1],
        //         ['id' => 10, 'name' => 'المدينة المنورة', 'region_id' => 2],
        //         ['id' => 11, 'name' => 'أخري', 'region_id' => 5],
        //     ];

        //     foreach ($cities as $city) {
        //         City::updateOrCreate(['id' => $city['id']], [
        //             'name' => $city['name'],
        //             'region_id' => $city['region_id'],
        //         ]);
        //     }

        //     // Neighborhoods
        //     $neighborhoods = [
        //         ['id' => 1, 'name' => 'الصالحية', 'city_id' => 1],
        //         ['id' => 2, 'name' => 'الشهابية', 'city_id' => 2],
        //         ['id' => 3, 'name' => 'صويدرة', 'city_id' => 3],
        //         ['id' => 4, 'name' => 'الرقيقة', 'city_id' => 4],
        //         ['id' => 5, 'name' => 'أخري', 'city_id' => 11],
        //     ];

        //     foreach ($neighborhoods as $neighborhood) {
        //         Neighborhood::updateOrCreate(['id' => $neighborhood['id']], [
        //             'name' => $neighborhood['name'],
        //             'city_id' => $neighborhood['city_id'],
        //         ]);
        //     }
        // المناطق
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

        // المدن
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

        // الأحياء
        $neighborhoods = [
            // أحياء الرياض
            ['id' => 1, 'name' => 'حي العليا', 'city_id' => 1],
            ['id' => 2, 'name' => 'حي النرجس', 'city_id' => 1],
            // أحياء جدة
            ['id' => 3, 'name' => 'حي النسيم', 'city_id' => 4],
            ['id' => 4, 'name' => 'حي الفيصلية', 'city_id' => 4],
            // أحياء الدمام
            ['id' => 5, 'name' => 'حي الشاطئ', 'city_id' => 7],
            ['id' => 6, 'name' => 'حي المزروعية', 'city_id' => 7],
            // أحياء المدينة المنورة
            ['id' => 7, 'name' => 'حي قربان', 'city_id' => 10],
            ['id' => 8, 'name' => 'حي العيون', 'city_id' => 10],
        ];

        foreach ($neighborhoods as $neighborhood) {
            Neighborhood::updateOrCreate(['id' => $neighborhood['id']], [
                'name' => $neighborhood['name'],
                'city_id' => $neighborhood['city_id'],
            ]);
        }
    }
}
