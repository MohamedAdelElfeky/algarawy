<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $projects = $this->projectService->getProjects($perPage, $page);        
        return response()->json($projects, 200);
    }
  
    public function show($id)
    {
        return new ProjectResource($this->projectService->getProjectById($id));
    }

    public function store(ProjectRequest $request)
    {
        $project = $this->projectService->createProject($request);
        return response()->json($project, 201);
    }

    public function update(ProjectRequest $request, $id)
    {
        $project = $this->projectService->getProjectById($id);
        $updatedProject = $this->projectService->updateProject($project, $request);
        return response()->json($updatedProject);
    }

    public function destroy($id)
    {
        return $this->projectService->deleteProject($id);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $results = $this->projectService->searchProject($searchTerm);
        return response()->json(['data' => $results]);
    }
}
