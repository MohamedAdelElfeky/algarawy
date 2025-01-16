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
        $regions = [
            ['id' => 1, 'name' => 'الجنوبية'],
            ['id' => 2, 'name' => 'الشرقية'],
            ['id' => 3, 'name' => 'الشمالية'],
            ['id' => 4, 'name' => 'الغربية'],
            ['id' => 5, 'name' => 'أخري'],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(['id' => $region['id']], ['name' => $region['name']]);
        }

        // Cities
        $cities = [
            ['id' => 1, 'name' => 'الهفوف', 'region_id' => 1],
            ['id' => 2, 'name' => 'ضياء', 'region_id' => 2],
            ['id' => 3, 'name' => 'الدرعية', 'region_id' => 3],
            ['id' => 4, 'name' => 'الطائف', 'region_id' => 4],
            ['id' => 5, 'name' => 'الجييل', 'region_id' => 1],
            ['id' => 6, 'name' => 'مكة', 'region_id' => 2],
            ['id' => 7, 'name' => 'تبوك', 'region_id' => 3],
            ['id' => 8, 'name' => 'الدمام', 'region_id' => 1],
            ['id' => 9, 'name' => 'جدة', 'region_id' => 1],
            ['id' => 10, 'name' => 'المدينة المنورة', 'region_id' => 2],
            ['id' => 11, 'name' => 'أخري', 'region_id' => 5],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(['id' => $city['id']], [
                'name' => $city['name'],
                'region_id' => $city['region_id'],
            ]);
        }

        // Neighborhoods
        $neighborhoods = [
            ['id' => 1, 'name' => 'الصالحية', 'city_id' => 1],
            ['id' => 2, 'name' => 'الشهابية', 'city_id' => 2],
            ['id' => 3, 'name' => 'صويدرة', 'city_id' => 3],
            ['id' => 4, 'name' => 'الرقيقة', 'city_id' => 4],
            ['id' => 5, 'name' => 'أخري', 'city_id' => 11],
        ];

        foreach ($neighborhoods as $neighborhood) {
            Neighborhood::updateOrCreate(['id' => $neighborhood['id']], [
                'name' => $neighborhood['name'],
                'city_id' => $neighborhood['city_id'],
            ]);
        }
    }
}
