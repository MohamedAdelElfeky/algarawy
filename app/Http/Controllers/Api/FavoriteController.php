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
use App\Models\Course;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function addFavorite(Request $request, $type, $id)
    {
        // Get the authenticated user using Auth::user()
        $user = Auth::user();

        // Check if the specified model type is valid
        $validModels = ['course', 'job', 'discount', 'meeting', 'project', 'service'];
        if (!in_array($type, $validModels)) {
            return response()->json(['message' => 'Invalid model type'], 400);
        }

        // Convert the model type to the appropriate class namespace
        $modelClass = 'App\Models\\' . ucfirst($type);

        // Check if the model with the given ID exists
        $model = $modelClass::find($id);
        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        // Check if the model is already a favorite for the user
        $existingFavorite = $user->favorites()->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $id)
            ->first();

        if ($existingFavorite) {
            // If the model is already a favorite, return a response indicating that it's already a favorite.
            return response()->json(['message' => 'Model is already a favorite'], 409);
        }

        // If the model is not already a favorite, add it as a favorite
        $favorite = new Favorite();
        $favorite->favoritable_id = $id;
        $favorite->favoritable_type = $modelClass;
        $user->favorites()->save($favorite);

        // Optionally, you can return a success response
        return response()->json(['message' => 'Model added to favorites'], 201);
    }

    public function getUserFavorites()
    {
        $user = Auth::user();
        $favorites = $user->favorites;
        $formattedFavorites = $favorites->map(function ($favorite) {
            switch ($favorite->favoritable_type) {
                case 'App\Models\Course':
                    return [
                        'model' => 'Course',
                        'data' => new CourseResource($favorite->favoritable)
                    ];
                case 'App\Models\Job':
                    return [
                        'model' => 'Job',
                        'data' => new JobResource($favorite->favoritable)
                    ];
                case 'App\Models\Discount':
                    return [
                        'model' => 'Discount',
                        'data' => new DiscountResource($favorite->favoritable)
                    ];
                case 'App\Models\Meeting':
                    return [
                        'model' => 'Meeting',
                        'data' => new MeetingResource($favorite->favoritable)
                    ];
                case 'App\Models\Project':
                    return [
                        'model' => 'Project',
                        'data' => new ProjectResource($favorite->favoritable)
                    ];
                case 'App\Models\Service':
                    return [
                        'model' => 'Service',
                        'data' => new ServiceResource($favorite->favoritable)
                    ];
                default:
                    // Handle other model types if needed
                    return null;
            }
        })->filter();

        return response()->json(['data' => $formattedFavorites]);
    }
}
