<?php

namespace App\Domain\Services\V2;

use App\Domain\DTO\V2\SearchUserDTO;
use App\Domain\Services\PaginationService;
use App\Filters\V2\UserSearchPipeline;
use App\Http\Resources\UserResource;

class UserSearchService
{
    public function __construct(
        private PaginationService $paginationService
    ) {}
    public function searchUsers(SearchUserDTO $dto)
    {
        $users = UserSearchPipeline::apply($dto);
        $usersResource = UserResource::collection($users);
        return [
            'data' => UserResource::collection($users),
            'metadata' => $this->paginationService->getPaginationData($usersResource),
        ];
    }
}