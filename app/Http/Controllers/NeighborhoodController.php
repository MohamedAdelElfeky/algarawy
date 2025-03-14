<?php
namespace App\Http\Controllers;

use App\Domain\Models\City;
use App\Domain\Models\Neighborhood;
use App\Domain\Models\Region;
use App\Domain\Services\NeighborhoodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    protected NeighborhoodService $neighborhoodService;

    public function __construct(NeighborhoodService $neighborhoodService)
    {
        $this->neighborhoodService = $neighborhoodService;
    }

    /**
     * Display a listing of the neighborhoods.
     */
    public function index()
    {
        $regions = Region::all();
        $cities = City::all();
        $neighborhoods = $this->neighborhoodService->getAllNeighborhoodsPaginated();

        return view('pages.dashboards.neighborhoods.index', compact('cities', 'regions', 'neighborhoods'));
    }

    /**
     * Store a new neighborhood.
     */
    public function addNeighborhood(Request $request): JsonResponse
    {
        $this->neighborhoodService->saveNeighborhood($request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]));

        return response()->json(['message' => 'تم إضافة / تحديث الحي بنجاح']);
    }

    /**
     * Remove a neighborhood.
     */
    public function destroy(Neighborhood $neighborhood): JsonResponse
    {
        $this->neighborhoodService->deleteNeighborhood($neighborhood);

        return response()->json(['message' => 'تم حذف الحي بنجاح']);
    }
}
