<?php

namespace App\Applications\UseCases;

use App\Applications\DTOs\ProjectDTO;
use App\Domain\Repositories\ProjectRepository;
use Illuminate\Support\Facades\Auth;

class CreateProjectUseCase
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function execute(ProjectDTO $projectDTO)
    {
        $data = [
            'description' => $projectDTO->description,
            'location' => $projectDTO->location,
            'status' => $projectDTO->status,
            'user_id' => Auth::id(),
        ];

        return $this->projectRepository->createProject($data);
    }
}
