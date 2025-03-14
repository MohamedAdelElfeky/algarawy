<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Neighborhood;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NeighborhoodRepository
{
    /**
     * Get paginated list of neighborhoods.
     */
    public function getAllPaginated(int $perPage = 25): LengthAwarePaginator
    {
        return Neighborhood::with('city')->paginate($perPage);
    }

    /**
     * Get all neighborhoods.
     */
    public function getAll(): Collection
    {
        return Neighborhood::with('city')->get();
    }

    /**
     * Get neighborhoods by city.
     */
    public function getByCity(int $cityId): Collection
    {
        return Neighborhood::where('city_id', $cityId)->get();
    }

    /**
     * Store or update a neighborhood.
     */
    public function storeOrUpdate(array $data): Neighborhood
    {
        return Neighborhood::updateOrCreate(
            ['name' => $data['name'], 'city_id' => $data['city_id']],
            $data
        );
    }

    /**
     * Delete a neighborhood.
     */
    public function delete(Neighborhood $neighborhood): void
    {
        $neighborhood->delete();
    }
}
