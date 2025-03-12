<?php

namespace App\Http\Controllers;

use App\Domain\Models\Project;
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
            ->paginate(25);
        return view('pages.dashboards.project.index', compact('projects'));
    }


    public function destroy($id)
    {
        return $this->projectService->deleteProject($id);
    }
}
