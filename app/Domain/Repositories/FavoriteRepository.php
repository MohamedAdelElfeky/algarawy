<?php

namespace App\Domain\Repositories;


class FavoriteRepository
{
    public function toggle($user, $model)
    {
        $existingFavorite = $user->favorites()
            ->where('favoritable_type', get_class($model))
            ->where('favoritable_id', $model->id)
            ->first();

        if ($existingFavorite) {
            $existingFavorite->delete();
            return ['message' => 'تم إزالة من المفضلة', 'favorited' => false];
        }

        $user->favorites()->create([
            'favoritable_id' => $model->id,
            'favoritable_type' => get_class($model),
        ]);

        return ['message' => 'تمت إضافة النموذج إلى المفضلة', 'favorited' => true];
    }

    public function getFavoritesByUser($user)
    {
        return $user->favorites->map(fn($favorite) => $this->mapFavoriteToResource($favorite))->filter();
    }

    private function mapFavoriteToResource($favorite)
    {
        $resourceMapping = [
            'App\Domain\Models\Course' => \App\Http\Resources\CourseResource::class,
            'App\Domain\Models\Job' => \App\Http\Resources\JobResource::class,
            'App\Domain\Models\Discount' => \App\Http\Resources\DiscountResource::class,
            'App\Domain\Models\Meeting' => \App\Http\Resources\MeetingResource::class,
            'App\Domain\Models\Project' => \App\Http\Resources\ProjectResource::class,
            'App\Domain\Models\Service' => \App\Http\Resources\ServiceResource::class,
        ];

        if (isset($resourceMapping[$favorite->favoritable_type]) && $favorite->favoritable) {
            return [
                'type' => class_basename($favorite->favoritable_type),
                'data' => new $resourceMapping[$favorite->favoritable_type]($favorite->favoritable),
            ];
        }

        return null;
    }
}
