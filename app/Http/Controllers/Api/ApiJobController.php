<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiJobController extends Controller
{
    protected $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    public function index(): JsonResponse
    {
        $jobs = $this->jobService->getAllJobs();
        return response()->json($jobs, 200);
    }

    public function show($id): JsonResponse
    {
        $job = $this->jobService->getJobById($id);
        return response()->json($job, 200);
    }
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $job = $this->jobService->createJob($data);
        return response()->json($job, 201);
    }
    public function update(Request $request, $id): JsonResponse
    {
        $job = $this->jobService->getJobById($id);
        $updatedJob = $this->jobService->updateJob($job, $request->all());

        return response()->json($updatedJob);
    }
    public function destroy($id): JsonResponse
    {
        $job = $this->jobService->getJobById($id);
        $this->jobService->deleteJob($job);
        return response()->json(['message' => 'Job deleted successfully'], 200);
    }
}