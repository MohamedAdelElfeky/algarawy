<?php

namespace App\Http\Controllers;

use App\Http\Resources\NeighborhoodResource;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Region;
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNeighborhood(Request $request)
    {
        $name = $request->input('name');
        $city_id = $request->input('city_id');
        Neighborhood::updateOrCreate(
            ['name' => $name, 'city_id' => $city_id]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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
