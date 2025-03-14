<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\City;
use App\Domain\Services\NeighborhoodService;
use App\Http\Controllers\Controller;
use App\Http\Resources\NeighborhoodResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NeighborhoodController extends Controller
{

    public function __construct(private NeighborhoodService $neighborhoodService) {}

    /**
     * Fetch all neighborhoods.
     */
    public function index(): AnonymousResourceCollection
    {
        return NeighborhoodResource::collection($this->neighborhoodService->getAllNeighborhoods());
    }

    /**
     * Get neighborhoods by a specific city.
     */
    public function getNeighborhoodsByCity(City $city): AnonymousResourceCollection
    {
        return NeighborhoodResource::collection($this->neighborhoodService->getNeighborhoodsByCity($city->id));
    }
}
