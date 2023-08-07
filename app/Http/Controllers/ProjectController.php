<?php

namespace App\Http\Controllers;

use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        $projects = $this->projectService->getAllProjects();
        return response()->json($projects, 200);
    }


    public function show($id)
    {
        return $this->projectService->getProjectById($id);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $project = $this->projectService->createProject($data);
        return response()->json($project);
    }

    // public function update(Request $request, $id)
    // {
    //     $project = $this->projectService->getProjectById($id);
    //     $updatedProject = $this->projectService->updateProject($project, $request->all());
    //     return response()->json($updatedProject);
    // }

    public function destroy($id)
    {
        return $this->projectService->deleteProject($id);
    }
}
