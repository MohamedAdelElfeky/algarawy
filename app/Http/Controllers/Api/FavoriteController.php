<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\FavoriteService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FavoriteController extends Controller
{
    public function __construct(private FavoriteService $favoriteService)
    {
        $this->middleware('auth:sanctum');
    }

    public function toggleFavorite(Request $request, $type, $id)
    {
        try {
            $result = $this->favoriteService->toggleFavorite($type, $id);
            return response()->json($result, 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'النموذج غير موجود'], 404);
        }
    }

    public function getUserFavorites()
    {
        return response()->json([
            'data' => $this->favoriteService->getUserFavorites(),
        ]);
    }
}
