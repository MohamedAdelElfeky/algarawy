<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\JobApplication;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        return response()->json(new JobResource($job), 200);
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $job = $this->jobService->createJob($data);
        return response()->json($job, 201);
    }
    public function update(Request $request, $id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'لم يتم العثور على الوظيفة'], 404);
        }
        $updatedJob = $this->jobService->updateJob($job, $request->all());
        return response()->json($updatedJob);
    }
    public function destroy($id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'لم يتم العثور على الوظيفة'], 404);
        }
        $jobApplicationsToDelete = JobApplication::where('job_id', $id)->get();
        foreach ($jobApplicationsToDelete as $application) {
            $application->delete();
        }
        $this->jobService->deleteJob($job);
        return response()->json(['message' => 'تم حذف الوظيفة بنجاح'], 200);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $results = $this->jobService->searchJob($searchTerm);
        return response()->json(['data' => $results]);
    }

    public function ChangeStatus(Request $request)
    {
        $job = Job::find($request->id);
        if (!$job) {
            return response()->json(['message' => 'لم يتم العثور على الوظيفة'], 404);
        }
        $validator = Validator::make($request->all(), [
            'job_status' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $status = $request->input('job_status');
        $data['job_status'] = $status;
        $job->update($data);
        return response()->json([
            'message' => 'تم تحديث حاله الوظيفية',
            'data' => new JobResource($job),
        ], 201);
    }
}
