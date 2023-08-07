<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class ApiProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $projects = $this->projectService->getAllProjects($perPage, $page);
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
        return response()->json($project, 201);
    }

    public function update(Request $request, $id)
    {
        $project = $this->projectService->getProjectById($id);
        $updatedProject = $this->projectService->updateProject($project, $request->all());
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
