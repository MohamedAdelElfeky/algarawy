<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NeighborhoodResource;
use App\Models\City;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

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
