<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\City;
use App\Domain\Models\Region;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;

class CityController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $cities = City::all();
        return CityResource::collection($cities);
    }

    public function getCitiesByRegion(Region $region)
    {
        $cities = $region->cities;
        return CityResource::collection($cities);
    }
}
