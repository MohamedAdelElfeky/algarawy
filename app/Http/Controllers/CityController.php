<?php

namespace App\Http\Controllers;

use App\Domain\Models\City;
use App\Domain\Models\Region;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = Region::all();
        $cities = City::paginate(25);
        return view('pages.dashboards.cities.index', compact('cities', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $name = $request->input('name');
        $region_id = $request->input('region_id');
        City::updateOrCreate(
            ['name' => $name, 'region_id' => $region_id]
        );
        return response()->json(['message' => 'تم إضافة / تحديث الرقم بنجاح']);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $name = $request->input('name');
        $region_id = $request->input('region_id');
        City::updateOrCreate(
            ['name' => $name, 'region_id' => $region_id]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $city = City::findOrFail($id);
        $city->neighborhoods()->delete();
        $city->delete();
    }
}
