<?php

namespace App\Services;

use App\Http\Resources\JobApplication2Resource;
use App\Http\Resources\JobApplicationResource;
use App\Http\Resources\JobResource;
use App\Models\FilePdf;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobApplicationService
{
    public function createJobApplication(array $data)
    {
        $existingJobApplication = JobApplication::where('user_id', Auth::id())
            ->where('job_id', $data['job_id'])
            ->first();

        if ($existingJobApplication) {
            return 'لقد تقدمت بالفعل لهذه الوظيفة.';
        }
        $validator = Validator::make($data, [
            'job_id' => 'required',
            'files*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4',

        ]);
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $data['user_id'] = Auth::id();
        $jobApplication = JobApplication::create($data);
        // Handle images/videos
        if (request()->hasFile('files')) {
            foreach (request()->file('files') as $key => $item) {
                $pdf = $data['files'][$key];
                $pdfType = $pdf->getClientOriginalExtension();
                $mimeType = $pdf->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_jobApplication.' . $pdf->getClientOriginalExtension();
                $pdf->move(public_path('jobApplication/files/'), $file_name);
                $pdfPath = "jobApplication/files/" . $file_name;
                $pdfObject = new FilePdf([
                    'url' => $pdfPath,
                    'mime' => $mimeType,
                    'type' => $pdfType,
                ]);
                $jobApplication->pdfs()->save($pdfObject);
            }
        }
        return  new JobResource(Job::find($jobApplication->job));
    }

    public function updateJobApplication($application_id, $data)
    {
        $validator = Validator::make($data, [
            'job_id' => 'nullable',
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
        return JobApplication::destroy($application_id);
    }

    public function getJobApplicationById($application_id)
    {
        return JobApplication::find($application_id);
    }

    public function getAllJobsApplication()
    {
        return JobApplication::all();
    }
    public function getJobApplicationCount($jobId)
    {
        $count = JobApplication::where('job_id', $jobId)->count();
        return $count;
    }

    public function getJobApplicationsForUserAndJob($jobId)
    {
        $userId = Auth::id();

        $jobApplications = JobApplication::where('job_id', $jobId)->where('user_id', $userId)    
            ->get();

        return JobApplication2Resource::collection($jobApplications);
    }

    public function getJobApplicationsByUserId()
    {
        $jobApplications = JobApplication::where('user_id', Auth::id())->get();
        return $jobApplications;
    }
}
