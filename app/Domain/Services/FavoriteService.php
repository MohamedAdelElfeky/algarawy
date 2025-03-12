<?php

namespace App\Domain\Services;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\FavoriteRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FavoriteService
{
    protected $validModels = [
        'course' => 'App\Domain\Models\Course',
        'job' => 'App\Domain\Models\Job',
        'discount' => 'App\Domain\Models\Discount',
        'meeting' => 'App\Domain\Models\Meeting',
        'project' => 'App\Domain\Models\Project',
        'service' => 'App\Domain\Models\Service',
    ];

    public function __construct(private FavoriteRepository $favoriteRepository)
    {
    }

    public function toggleFavorite(string $type, int $id)
    {
        if (!isset($this->validModels[$type])) {
            throw new \InvalidArgumentException('نوع النموذج غير صالح');
        }

        $modelClass = $this->validModels[$type];
        $user = Auth::guard('sanctum')->user();
        $model = $modelClass::find($id);

        if (!$model) {
            throw new ModelNotFoundException('النموذج غير موجود');
        }

        return $this->favoriteRepository->toggle($user, $model);
    }

    public function getUserFavorites()
    {
        $user = Auth::guard('sanctum')->user();
        return $this->favoriteRepository->getFavoritesByUser($user);
    }
}
