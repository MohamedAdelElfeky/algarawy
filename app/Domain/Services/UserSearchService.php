<?php

namespace App\Domain\Services;

use App\Domain\DTO\SearchUserDTO;
use App\Filters\UserSearchPipeline;
use App\Http\Resources\UserResource;

class UserSearchService
{
    public function searchUsers(SearchUserDTO $dto)
    {
        $users = UserSearchPipeline::apply($dto);
        return UserResource::collection($users);
    }
}