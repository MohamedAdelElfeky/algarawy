<?php

namespace App\Http\Controllers;

use App\Domain\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class JobController extends Controller
{
    public function __construct(private JobService $jobService) {}

    public function index(): View
    {
        $jobs = $this->jobService->getPaginated(25);
        return view('pages.dashboards.job.index', compact('jobs'));
    }


    public function destroy(int $id): JsonResponse
    {
        
        $deleted = $this->jobService->deleteJob($id, 'web');
        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'deleted successfully.' : 'Failed to delete.'
        ]);
    }
}
