<?php

namespace App\Domain\Repositories;

use App\Domain\Models\City;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CityRepository
{
    /**
     * Get paginated list of cities.
     */
    public function getAllPaginated(int $perPage = 25): LengthAwarePaginator
    {
        return City::with('region')->paginate($perPage);
    }

    /**
     * Get all cities.
     */
    public function getAll(): Collection
    {
        return City::with('region')->get();
    }

    /**
     * Get cities by region.
     */
    public function getByRegion(int $regionId): Collection
    {
        return City::where('region_id', $regionId)->get();
    }

    /**
     * Store or update a city.
     */
    public function storeOrUpdate(array $data): City
    {
        return City::updateOrCreate(
            ['name' => $data['name'], 'region_id' => $data['region_id']],
            $data
        );
    }

    /**
     * Delete a city.
     */
    public function delete(City $city): void
    {
        $city->neighborhoods()->delete();
        $city->delete();
    }
}
