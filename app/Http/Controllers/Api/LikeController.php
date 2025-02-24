<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function toggleLike(Request $request, $type, $id)
    {
        $user = Auth::user();
        $validModels = ['course', 'job', 'discount', 'meeting', 'project', 'service'];
        if (!in_array($type, $validModels)) {
            return response()->json(['message' => 'نوع النموذج غير صالح'], 400);
        }
        $modelClass = 'App\Models\\' . ucfirst($type);
        $model = $modelClass::find($id);
        if (!$model) {
            return response()->json(['message' => 'النموذج غير موجود'], 404);
        }
        $existingLike = $user->likes()->where('likable_type', $modelClass)
            ->where('likable_id', $id)
            ->first();
        if ($existingLike) {
            $existingLike->delete();
            return response()->json(['message' => 'تم إزالة الإعجاب', 'liked' => false], 200);
        }
        $like = new Like();
        $like->likable_id = $id;
        $like->likable_type = $modelClass;
        $user->likes()->save($like);
        return response()->json(['message' => 'تم الإعجاب', 'liked' => true], 200);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
        //
    }
}
