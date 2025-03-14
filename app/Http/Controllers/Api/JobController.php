<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\JobService;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct(private JobService $jobService)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $Jobs = $this->jobService->getJobs($perPage, $page);
        return response()->json($Jobs, 200);
    }

    public function show($id)
    {
        return new JobResource($this->jobService->getJobById($id));
    }

    public function store(JobRequest $request)
    {
        $Job = $this->jobService->createJob($request);
        return response()->json($Job, 201);
    }

    public function update(JobRequest $request, $id)
    {
        $job = $this->jobService->getJobById($id);
        $updatedJob = $this->jobService->updateJob($job, $request);
        return response()->json($updatedJob);
    }

    public function destroy($id)
    {
        return $this->jobService->deleteJob($id);
    }

    public function search(Request $request)
    {
        return $this->jobService->searchJob($request->get('search'));
    }
}

