<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\DiscountResource;
use App\Http\Resources\JobResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request, $type, $id)
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
        $existingFavorite = $user->favorites()->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $id)
            ->first();
        if ($existingFavorite) {
            $existingFavorite->delete();
            return response()->json(['message' => 'تم إزالة من المفضلة', 'favorited' => false], 200);
        }
        $favorite = new Favorite();
        $favorite->favoritable_id = $id;
        $favorite->favoritable_type = $modelClass;
        $user->favorites()->save($favorite);
        return response()->json(['message' => 'تمت إضافة النموذج إلى المفضلة', 'favorited' => true], 200);
    }

    public function getUserFavorites()
    {
        $user = Auth::user();
        $favorites = $user->favorites;
        $formattedFavorites = $favorites->map(function ($favorite) {
            switch ($favorite->favoritable_type) {
                case 'App\Models\Course':
                    return [
                        'type' => 'Course',
                        'data' => new CourseResource($favorite->favoritable)
                    ];
                case 'App\Models\Job':
                    return [
                        'type' => 'Job',
                        'data' => new JobResource($favorite->favoritable)
                    ];
                case 'App\Models\Discount':
                    return [
                        'type' => 'Discount',
                        'data' => new DiscountResource($favorite->favoritable)
                    ];
                case 'App\Models\Meeting':
                    return [
                        'type' => 'Meeting',
                        'data' => new MeetingResource($favorite->favoritable)
                    ];
                case 'App\Models\Project':
                    return [
                        'type' => 'Project',
                        'data' => new ProjectResource($favorite->favoritable)
                    ];
                case 'App\Models\Service':
                    return [
                        'type' => 'Service',
                        'data' => new ServiceResource($favorite->favoritable)
                    ];
                default:
                    return null;
            }
        })->filter();

        return response()->json(['data' => $formattedFavorites]);
    }
}
