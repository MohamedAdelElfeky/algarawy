<?php

namespace App\Filters\V2;

use App\Domain\DTO\V2\SearchUserDTO;
use App\Filters\User\CityFilter;
use App\Filters\User\NeighborhoodFilter;
use App\Filters\User\RegionFilter;
use App\Filters\User\SearchFilter;
use App\Models\User;
use Illuminate\Pipeline\Pipeline;

class UserSearchPipeline
{
    public static function apply(SearchUserDTO $dto)
    {
        return app(Pipeline::class)
            ->send(User::query())
            ->through([
                new SearchFilter($dto->searchTerm),
                new RegionFilter($dto->regionId),
                new CityFilter($dto->cityId),
                new NeighborhoodFilter($dto->neighborhoodId),
            ])
            ->thenReturn()
            ->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }
}