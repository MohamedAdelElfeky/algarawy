<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
        $projects = Project::with(['images', 'pdfs', 'favorites', 'likes'])
            ->get();
        return view('pages.dashboards.project.index', compact('projects'));
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

    public function changeStatus(Request $request, Project $project)
    {
        $request->validate([
            'status' => 'in:public,private',
        ]);
        $project->update(['status' => $request->status]);

        return back()->with('status', 'Project status updated successfully!');
    }
}
