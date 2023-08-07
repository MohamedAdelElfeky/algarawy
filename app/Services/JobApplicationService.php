<?php

namespace App\Services;

use App\Http\Resources\JobApplicationResource;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobApplicationService
{
    public function createJobApplication(array $data)
    {
        $validator = Validator::make($data, [
            'job_id' => 'required',
            'file' => 'required',
            // 'type_file' => 'required',
        ]);
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $data['user_id'] = Auth::id();
        $data['type_file'] = Auth::id();

        $jobApplication = JobApplication::create($data);

        return $jobApplication;
    }

    public function updateJobApplication($application_id, $data)
    {
        $validator = Validator::make($data, [
            'user_id' => 'nullable',
            'job_id' => 'nullable',
            'file' => 'nullable',
            'type_file' => 'nullable',
        ]);
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $jobApplication = JobApplication::find($application_id);
        if ($jobApplication) {
            $jobApplication->update($data);
            return $jobApplication;
        }

        return null;
    }

    public function deleteJobApplication($application_id)
    {
        // Find and delete the job application with the given application_id
        return JobApplication::destroy($application_id);
    }

    public function getJobApplicationById($application_id)
    {
        // Find and return the job application with the given application_id
        return JobApplication::find($application_id);
    }

    public function getAllJobsApplication()
    {
        // Return all job applications
        return JobApplication::all();
    }
    
}
