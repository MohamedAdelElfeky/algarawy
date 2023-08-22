<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }
    public function index()
    {
        // $jobQuery = Job::orderBy('created_at', 'desc')->get();
        $jobQuery = Job::with([
            'region', 'city', 'neighborhood',
            'companyRegion', 'companyCity', 'companyNeighborhood',
            'user', 'images', 'pdfs', 'likes', 'favorites',
        ])->orderBy('created_at', 'desc')->get();
        $jobs = JobResource::collection($jobQuery);
        return view('pages.dashboards.job.index', compact('jobs'));
    }

    public function show($id)
    {
        // Find the job by ID
        $job = $this->jobService->getJobById($id);

        if (!$job) {
            return response()->json([
                'message' => 'Job not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Job retrieved successfully',
            'data' => $job,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        // Call the service to create the job
        $response = $this->jobService->createJob($data);

        return $response;
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        // Find the job
        $job = Job::findOrFail($id);

        // Call the service to update the job
        $response = $this->jobService->updateJob($job, $data);

        return $response;

        // Update the job
    }

    public function destroy($id)
    {
        // Find the job
        $job = Job::findOrFail($id);

        // Call the service to delete the job
        $response = $this->jobService->deleteJob($job);

        return $response;
    }
}
