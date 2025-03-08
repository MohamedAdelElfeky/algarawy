<?php

namespace App\Services;

use App\Domain\Models\FilePdf;
use App\Domain\Models\Job;
use App\Domain\Models\JobApplication;
use App\Http\Resources\JobApplication2Resource;
use App\Http\Resources\JobApplicationResource;
use App\Http\Resources\JobResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobApplicationService
{
    public function createJobApplication(array $data)
    {      
        
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
        return  new JobResource(Job::find($data['job_id']));
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

        $jobApplications = JobApplication::where('job_id', $jobId)
            ->get();

        return JobApplication2Resource::collection($jobApplications);
    }

    public function getJobApplicationsByUserId()
    {
        $jobApplications = JobApplication::where('user_id', Auth::id())->get();
        return $jobApplications;
    }
}
