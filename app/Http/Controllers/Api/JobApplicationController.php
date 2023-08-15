<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobApplicationResource;
use App\Models\Job;
use App\Models\JobApplication;
use App\Services\JobApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{

    private $jobApplicationService;

    public function __construct(JobApplicationService $jobApplicationService)
    {
        $this->jobApplicationService = $jobApplicationService;
    }

    public function store(Request $request)
    {
        $existingJob = Job::find($request->job_id);
        if (!$existingJob) {
            return response()->json(['message' => 'لم يتم العثور على الوظيفة'], 403);
        }
        $existingJobApplication = JobApplication::where('user_id', Auth::id())
            ->where('job_id', $request->job_id)
            ->first();

        if ($existingJobApplication) {
            return response()->json(['message' => 'لقد تقدمت بالفعل لهذه الوظيفة.'], 403);
        }        
        $jobApplication = $this->jobApplicationService->createJobApplication($request->all());
        return response()->json(['message' => $jobApplication], 201);
    }

    public function update(Request $request, $id)
    {
        $jobApplication = $this->jobApplicationService->updateJobApplication($id, $request->all());
        if (!$jobApplication) {
            return response()->json(['message' => 'Job application not found'], 404);
        }
        return response()->json($jobApplication, 200);
    }

    public function delete($id)
    {
        $deleted = $this->jobApplicationService->deleteJobApplication($id);
        if ($deleted) {
            return response()->json(['message' => 'Job application deleted successfully'], 200);
        }
        return response()->json(['message' => 'Job application not found'], 404);
    }

    public function show($id)
    {
        $jobApplication = $this->jobApplicationService->getJobApplicationById($id);

        if (!$jobApplication) {
            return response()->json(['message' => 'Job application not found'], 404);
        }

        return response()->json($jobApplication, 200);
    }

    public function index()
    {
        $allJobApplications = $this->jobApplicationService->getAllJobsApplication();

        if ($allJobApplications === null) {
            $errorMessage = "No job applications found.";
            return response()->json(['error' => $errorMessage], 403);
        }

        return response()->json($allJobApplications, 200);
    }


    public function getJobApplicationCount($jobId)
    {
        $count = $this->jobApplicationService->getJobApplicationCount($jobId);

        if ($count === null) {
            $errorMessage = "No job applications found for the specified job.";
            return response()->json(['error' => $errorMessage], 404);
        }

        return response()->json(['count' => $count], 200);
    }

    public function getJobApplicationsForUserAndJob($jobId)
    {
        $jobApplications = $this->jobApplicationService->getJobApplicationsForUserAndJob($jobId);
        if ($jobApplications === null) {
            $errorMessage = "No job applications found for the specified job.";
            return response()->json(['error' => $errorMessage], 404);
        }

        return response()->json($jobApplications, 200);
    }

    public function getJobApplicationsByUserId()
    {
        $jobApplications =  JobApplicationResource::collection($this->jobApplicationService->getJobApplicationsByUserId());
        if ($jobApplications === null) {
            $errorMessage = "No job applications found for the specified job.";
            return response()->json(['error' => $errorMessage], 404);
        }
        return response()->json($jobApplications, 200);
    }
}
