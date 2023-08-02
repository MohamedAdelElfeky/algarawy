<?php

namespace Database\Seeders;

use App\Models\Neighborhood;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NeighborhoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Neighborhood::create(['name' => 'الصالحية', 'city_id' => 1]);
        Neighborhood::create(['name' => 'الشهابية', 'city_id' => 2]);
        Neighborhood::create(['name' => 'صويدرة', 'city_id' => 3]);
        Neighborhood::create(['name' => 'الرقيقة', 'city_id' => 4]);
    }
}
