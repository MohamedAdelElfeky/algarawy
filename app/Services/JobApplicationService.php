<?php

namespace App\Services;

use App\Models\FilePdf;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobApplicationService
{
    public function createJobApplication(array $data)
    {
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
                $pdf->move(public_path('jobApplication/pdf/'), $file_name);
                $pdfPath = "jobApplication/pdf/" . $file_name;
                $pdfObject = new FilePdf([
                    'url' => $pdfPath,
                    'mime' => $mimeType,
                    'type' => $pdfType,
                ]);
                $jobApplication->pdfs()->save($pdfObject);
            }
        }
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
