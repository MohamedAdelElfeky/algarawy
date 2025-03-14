<?php

namespace App\Http\Controllers;

use App\Domain\Models\Project;
use App\Domain\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService) {}
    public function index(): View
    {
        $projects = $this->projectService->getPaginatedProjects(25);
        return view('pages.dashboards.project.index', compact('projects'));
    }


    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->projectService->deleteProject($id, 'web');
        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Project deleted successfully.' : 'Failed to delete project.'
        ]);
    }
}
