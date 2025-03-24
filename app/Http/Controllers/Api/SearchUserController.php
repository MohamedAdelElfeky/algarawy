<?php
namespace App\Http\Controllers\Api;

use App\Domain\DTO\SearchUserDTO;
use App\Domain\Services\UserSearchService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchUserController extends Controller
{
    public function __construct(private UserSearchService $userService) {}

    public function searchUser(Request $request)
    {
        $dto = SearchUserDTO::fromRequest($request);
        return response()->json($this->userService->searchUsers($dto));
    }
}