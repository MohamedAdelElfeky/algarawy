<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\City;
use App\Domain\Models\Neighborhood;
use App\Http\Controllers\Controller;
use App\Http\Resources\NeighborhoodResource;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    public function index()
    {
        $neighborhoods = Neighborhood::all();
        return NeighborhoodResource::collection($neighborhoods);
    }

    public function getNeighborhoodsByCity(City $city)
    {
        $neighborhoods = $city->neighborhoods;
        return NeighborhoodResource::collection($neighborhoods);
    }
}
