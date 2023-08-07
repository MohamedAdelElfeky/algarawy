<?php

namespace App\Services;

use App\Http\Resources\JobResource;
use App\Models\FilePdf;
use App\Models\Image;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class JobService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }


    public function getAllJobs($perPage = 10, $page = 1)
    {
        $jobs = Job::paginate($perPage, ['*'], 'page', $page);
        $jobCollection = JobResource::collection($jobs);

        $paginationData = $this->paginationService->getPaginationData($jobs);

        return [
            'data' => $jobCollection,
            'metadata' => $paginationData,
        ];
    }



    public function getJobById($id)
    {
        $job = Job::find($id);
        if (!$job) {
            abort(404, 'لم يتم العثور على الوظيفة');
        }
        return $job;
    }

    public function createJob(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'required',
            'qualifications' => 'required',
            'location' => 'string|location',
            'contact_information' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_name' => 'required|string',            
            'company_location' => 'required|string|location',
            'company_type' => 'required|string',
            'company_link' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'job_type' => 'required|string',
            'is_training' => 'required|boolean',
            'is_full_time' => 'required|boolean',
            'price' => 'required|numeric',
            'job_status' => 'required|boolean',
            'images_or_video.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4',
            'files_pdf.*' => 'required|file',
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
        if (request()->hasFile('images_or_video')) {
            foreach (request()->file('images_or_video') as $key => $item) {
                $image = $data['images_or_video'][$key];
                $imageType = $image->getClientOriginalExtension();
                $mimeType = $image->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_job.' . $image->getClientOriginalExtension();
                $image->move(public_path('job/images/'), $file_name);
                $imagePath = "job/images/" . $file_name;
                $imageObject = new Image([
                    'url' => $imagePath,
                    'mime' => $mimeType,
                    'image_type' => $imageType,
                ]);
                $job->images()->save($imageObject);
            }
        }

        // Handle images/videos
        if (request()->hasFile('files_pdf')) {
            foreach (request()->file('files_pdf') as $key => $item) {
                $pdf = $data['files_pdf'][$key];
                $pdfType = $pdf->getClientOriginalExtension();
                $mimeType = $pdf->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_job.' . $pdf->getClientOriginalExtension();
                $pdf->move(public_path('job/pdf/'), $file_name);
                $pdfPath = "job/pdf/" . $file_name;
                $pdfObject = new FilePdf([
                    'url' => $pdfPath,
                    'mime' => $mimeType,
                    'type' => $pdfType,
                ]);
                $job->pdfs()->save($pdfObject);
            }
        }

        return [
            'message' => 'تم إنشاء الوظيفة بنجاح',
            'data' => new JobResource($job),
        ];
    }

    public function updateJob(Job $job, array $data)
    {
        if (($job->user_id) != Auth::id()); {
            return response()->json([
                'message' => 'هذا الوظيفية ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'sometimes|required',
            'qualifications' => 'sometimes|required',
            'location' => 'sometimes|required',
            'contact_information' => 'sometimes|required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_name' => 'sometimes|required|string',
            'company_location' => 'sometimes|required|string',
            'company_type' => 'sometimes|required|string',
            'company_link' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'job_type' => 'sometimes|required|string',
            'is_training' => 'sometimes|required|boolean',
            'is_full_time' => 'sometimes|required|boolean',
            'price' => 'sometimes|required|numeric',
            'job_status' => 'sometimes|required|boolean',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $job->update($data);
        return response()->json([
            'message' => 'تم تحديث الوظيفة بنجاح',
            'data' => new JobResource($job),
        ]);
    }

    public function deleteJob(Job $job)
    {
        if (($job->user_id) != Auth::id()); {
            return response()->json([
                'message' => 'هذا الوظيفية ليس من إنشائك',
            ], 200);
        }
        $job->delete();
        return response()->json([
            'message' => 'تم حذف الوظيفة بنجاح',
        ]);
    }

    public function searchJob($searchTerm)
    {
        $jobs = Job::where(function ($query) use ($searchTerm) {
            $fields = ['description', 'name', 'qualifications', 'contact_information', 'company_name', 'price'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();
        return JobResource::collection($jobs);
    }
}
