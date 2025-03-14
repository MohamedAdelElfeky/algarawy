<?php

namespace App\Http\Controllers;

use App\Domain\Models\City;
use App\Domain\Models\Region;
use App\Domain\Services\CityService;
use App\Http\Requests\CityRequest;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    protected CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = $this->cityService->getAllCitiesPaginated();
        $regions = Region::all();

        return view('pages.dashboards.cities.index', compact('cities', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityRequest $request): JsonResponse
    {
        $this->cityService->saveCity($request->validated());

        return response()->json(['message' => 'تم إضافة / تحديث المدينة بنجاح']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CityRequest $request, City $city): JsonResponse
    {
        $this->cityService->saveCity($request->validated());

        return response()->json(['message' => 'تم تحديث المدينة بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city): JsonResponse
    {
        $this->cityService->deleteCity($city);

        return response()->json(['message' => 'تم حذف المدينة بنجاح']);
    }
}
