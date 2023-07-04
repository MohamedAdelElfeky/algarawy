<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class JobService
{
    public function getAllJobs()
    {
        $jobs = Job::all();
        return $jobs;
    }


    public function getJobById($id)
    {
        $job = Job::find($id);

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
            'user_id' => 'required|exists:users,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
            'data' => $job,
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
            'user_id' => 'required|exists:users,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
            'data' => $job,
        ]);
    }

    public function deleteJob(Job $job): JsonResponse
    {
        // Delete the job

        return response()->json([
            'message' => 'Job deleted successfully',
        ]);
    }
}
