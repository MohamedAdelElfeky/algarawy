<?php

namespace App\Domain\Services;

use App\Domain\Models\City;
use App\Domain\Repositories\CityRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CityService
{
    protected CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * Get paginated list of cities.
     */
    public function getAllCitiesPaginated(): LengthAwarePaginator
    {
        return $this->cityRepository->getAllPaginated();
    }

    /**
     * Get all cities.
     */
    public function getAllCities(): Collection
    {
        return $this->cityRepository->getAll();
    }

    /**
     * Get cities by region.
     */
    public function getCitiesByRegion(int $regionId): Collection
    {
        return $this->cityRepository->getByRegion($regionId);
    }

    /**
     * Create or update a city.
     */
    public function saveCity(array $data): City
    {
        return $this->cityRepository->storeOrUpdate($data);
    }

    /**
     * Delete a city.
     */
    public function deleteCity(City $city): void
    {
        $this->cityRepository->delete($city);
    }
}
