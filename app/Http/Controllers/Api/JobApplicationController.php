<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobApplicationResource;
use App\Services\JobApplicationService;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{

    private $jobApplicationService;

    public function __construct(JobApplicationService $jobApplicationService)
    {
        $this->jobApplicationService = $jobApplicationService;
    }

    public function store(Request $request)
    {
        $jobApplication = $this->jobApplicationService->createJobApplication($request->all());
        return response()->json($jobApplication, 201);
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
        return response()->json($allJobApplications, 200);
    }
}
