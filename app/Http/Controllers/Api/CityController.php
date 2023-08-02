<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Models\Region;

class CityController extends Controller
{
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
