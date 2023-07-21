<?php

namespace App\Services;

use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobService
{
    public function getAllJobs()
    {
        $jobs = Job::all();
        return JobResource::collection($jobs);
    }

    public function getJobById($id)
    {
        $job = Job::find($id);
        if (!$job) {
            abort(404, 'Job not found');
        }
        return $job;
    }

    public function createJob(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'required',
            'qualifications' => 'required',
            'location' => 'required',
            'contact_information' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_type' => 'required',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        // Create a new job
        $job = Job::create($data);
        return response()->json([
            'message' => 'Job created successfully',
            'data' => new JobResource($job),
        ]);
    }

    public function updateJob(Job $job, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'required',
            'qualifications' => 'required',
            'location' => 'required',
            'contact_information' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_type' => 'required',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        // Update the job
        $job->update($data);
        return response()->json([
            'message' => 'Job updated successfully',
            'data' => new JobResource($job),
        ]);
    }

    public function deleteJob(Job $job): JsonResponse
    {
        // Delete the job
        $job->delete();
        return response()->json([
            'message' => 'Job deleted successfully',
        ]);
    }
}
