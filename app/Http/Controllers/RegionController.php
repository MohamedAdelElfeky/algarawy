<?php

namespace App\Http\Controllers;

use App\Domain\Services\RegionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{

    public function __construct(private RegionService $regionService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = $this->regionService->getAllRegions();
        return view('pages.dashboards.regions.index', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->regionService->createOrUpdateRegion($request->input('name'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return $this->regionService->updateRegion($id, $request->input('name'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->regionService->deleteRegion($id);
    }
}
