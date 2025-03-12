<?php

namespace App\Http\Controllers;

use App\Domain\Models\City;
use App\Domain\Models\Neighborhood;
use App\Domain\Models\Region;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = Region::all();
        $cities = City::all();
        $neighborhoods = Neighborhood::paginate(25);
        return view('pages.dashboards.neighborhoods.index', compact('cities', 'regions', 'neighborhoods'));
    }

    public function addNeighborhood(Request $request)
    {
        $name = $request->input('name');
        $city_id = $request->input('city_id');
        Neighborhood::updateOrCreate(
            ['name' => $name, 'city_id' => $city_id]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $neighborhood = Neighborhood::findOrFail($id);
        $neighborhood->delete();
    }
}
