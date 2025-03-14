<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\RegionService;
use App\Http\Controllers\Controller;
use App\Http\Resources\RegionResource;
use Illuminate\Http\Request;

class RegionController extends Controller
{

    public function __construct(private RegionService $regionService) {}

    public function index()
    {
        $regions = $this->regionService->getAllRegions();
        return RegionResource::collection($regions);
    }
}
