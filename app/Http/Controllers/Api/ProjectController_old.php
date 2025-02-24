<?php

namespace App\Http\Controllers\Api;

use App\Applications\DTOs\ProjectDTO;
use App\Applications\UseCases\CreateProjectUseCase;
use App\Domain\Repositories\ProjectRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private $createProjectUseCase;
    private $projectRepository;

    public function __construct(CreateProjectUseCase $createProjectUseCase, ProjectRepository $projectRepository)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
        $this->createProjectUseCase = $createProjectUseCase;
        $this->projectRepository = $projectRepository;
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);

        return response()->json($this->projectRepository->getAllProjects($perPage, $page), 200);
    }

    public function show($id)
    {
        return response()->json($this->projectRepository->getProjectById($id), 200);
    }

    public function store(Request $request)
    {
        $projectDTO = ProjectDTO::fromArray($request->all());
        $project = $this->createProjectUseCase->execute($projectDTO);

        return response()->json($project, 201);
    }
}
