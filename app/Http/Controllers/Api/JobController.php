<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobService;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page');
        $page = $request->header('page');
        $jobs = $this->jobService->getAllJobs($perPage, $page);
        return response()->json($jobs, 200);
    }
    public function show($id)
    {
        $job = $this->jobService->getJobById($id);
        if (!$job) {
            return response()->json(['message' => 'لم يتم العثور على الوظيفة'], 404);
        }
        return response()->json($job, 200);
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $job = $this->jobService->createJob($data);
        return response()->json($job, 201);
    }
    public function update(Request $request, $id)
    {
        $job = $this->jobService->getJobById($id);
        $updatedJob = $this->jobService->updateJob($job, $request->all());
        return response()->json($updatedJob);
    }
    public function destroy($id)
    {
        $job = $this->jobService->getJobById($id);
        $this->jobService->deleteJob($job);
        return response()->json(['message' => 'تم حذف الوظيفة بنجاح'], 200);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $results = $this->jobService->searchJob($searchTerm);
        return response()->json(['data' => $results]);
    }
}
