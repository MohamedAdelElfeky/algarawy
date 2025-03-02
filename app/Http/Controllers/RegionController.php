<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegionResource;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = Region::paginate(25);
        return view('pages.dashboards.regions.index', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $name = $request->input('name');
        Region::updateOrCreate(
            ['name' => $name]
        );

        return response()->json(['message' => 'تم إضافة / تحديث الرقم بنجاح']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $name = $request->input('name');
        Region::updateOrCreate(
            ['name' => $name]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $region = Region::findOrFail($id);
        foreach ($region->cities as $city) {
            $city->neighborhoods()->delete();
            $city->delete();
        }
        $region->delete();
    }
}
