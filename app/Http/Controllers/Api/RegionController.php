<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\Region;
use App\Http\Controllers\Controller;
use App\Http\Resources\RegionResource;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        return RegionResource::collection($regions);
    }
}
