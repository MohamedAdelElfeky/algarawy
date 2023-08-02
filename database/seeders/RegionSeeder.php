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
        Region::create(['name' => 'الجنوبية']);
        Region::create(['name' => 'الشرقية']);
        Region::create(['name' => 'الشمالية']);
        Region::create(['name' => 'الغربية']);
    }
}
