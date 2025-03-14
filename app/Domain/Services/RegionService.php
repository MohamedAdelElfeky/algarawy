<?php

namespace App\Domain\Services;

use App\Domain\Models\Region;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RegionService
{
    public function getAllRegions(): LengthAwarePaginator
    {
        return Region::paginate(25);
    }

    public function createOrUpdateRegion(string $name): JsonResponse
    {
        Region::updateOrCreate(['name' => $name]);

        return response()->json(['message' => 'تم إضافة / تحديث المنطقة بنجاح']);
    }

    public function updateRegion(string $id, string $name): JsonResponse
    {
        $region = Region::findOrFail($id);
        $region->update(['name' => $name]);

        return response()->json(['message' => 'تم تحديث المنطقة بنجاح']);
    }

    public function deleteRegion(string $id): JsonResponse
    {
        DB::transaction(function () use ($id) {
            $region = Region::findOrFail($id);

            foreach ($region->cities as $city) {
                $city->neighborhoods()->delete();
                $city->delete();
            }

            $region->delete();
        });

        return response()->json(['message' => 'تم حذف المنطقة بنجاح']);
    }
}
