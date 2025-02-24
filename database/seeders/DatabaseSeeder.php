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
            RegionSeeder::class,
            CitySeeder::class,
            NeighborhoodSeeder::class,
            UsersSeeder::class,
            UserSettingSeeder::class,
            RolePermissionSeeder::class,
        ]);
     
        
    }
}
