<?php

namespace App\Http\Controllers;

use App\Domain\Models\Job;
use App\Http\Resources\JobResource;
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
        $jobQuery = Job::with([
            'region',
            'city',
            'neighborhood',
            'JobCompanies',            
            'user',
            'images',
            'pdfs',
            'likes',
            'favorites',
        ])->orderBy('created_at', 'desc')->paginate(25);
        $jobs = JobResource::collection($jobQuery);
        return view('pages.dashboards.job.index', compact('jobs'));
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
