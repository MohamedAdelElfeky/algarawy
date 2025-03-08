<?php

namespace Database\Seeders;

use App\Domain\Models\Neighborhood;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NeighborhoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
