<?php

namespace App\Domain\Services;

use App\Domain\Models\Neighborhood;
use App\Domain\Repositories\NeighborhoodRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NeighborhoodService
{
    protected NeighborhoodRepository $neighborhoodRepository;

    public function __construct(NeighborhoodRepository $neighborhoodRepository)
    {
        $this->neighborhoodRepository = $neighborhoodRepository;
    }

    /**
     * Get paginated neighborhoods.
     */
    public function getAllNeighborhoodsPaginated(): LengthAwarePaginator
    {
        return $this->neighborhoodRepository->getAllPaginated();
    }

    /**
     * Get all neighborhoods.
     */
    public function getAllNeighborhoods(): Collection
    {
        return $this->neighborhoodRepository->getAll();
    }

    /**
     * Get neighborhoods by city.
     */
    public function getNeighborhoodsByCity(int $cityId): Collection
    {
        return $this->neighborhoodRepository->getByCity($cityId);
    }

    /**
     * Create or update a neighborhood.
     */
    public function saveNeighborhood(array $data): Neighborhood
    {
        return $this->neighborhoodRepository->storeOrUpdate($data);
    }

    /**
     * Delete a neighborhood.
     */
    public function deleteNeighborhood(Neighborhood $neighborhood): void
    {
        $this->neighborhoodRepository->delete($neighborhood);
    }
}
