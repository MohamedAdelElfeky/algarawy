<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\Region;
use App\Domain\Services\CityService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CityController extends Controller
{

    public function __construct(private CityService $cityService) {}

    /**
     * Fetch all cities.
     */
    public function index(): AnonymousResourceCollection
    {
        return CityResource::collection($this->cityService->getAllCities());
    }

    /**
     * Get cities by a specific region.
     */
    public function getCitiesByRegion(Region $region): AnonymousResourceCollection
    {
        return CityResource::collection($this->cityService->getCitiesByRegion($region->id));
    }
}
