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
            return response()->json(['message' => 'لم يتم العثور على الوظيفة'], 404);
        }
        return $job;
    }

    public function createJob(array $data)
    {
        $validator = Validator::make($data, [
            'description' => 'nullable',
            'title' => 'nullable',
            'company_name' => 'nullable|string',
            'company_location' => 'nullable|string|location',
            'company_type' => 'nullable|string',
            'company_link' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'job_type' => 'nullable|string',
            'job_duration' => 'nullable',
            'price' => 'nullable|numeric',
            'job_status' => 'nullable|boolean',
            'images_or_video.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'company_region_id' => 'nullable|exists:regions,id',
            'company_city_id' => 'nullable|exists:cities,id',
            'company_neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'is_training' => 'nullable',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $imagePathCompanyLogo = "";
        if (request()->hasFile('company_logo')) {
            $imageCompanyLogo = request()->file('company_logo');
            $file_name_company_logo = time() . rand(0, 9999999999999) . '_company_logo.' . $imageCompanyLogo->getClientOriginalExtension();
            $imageCompanyLogo->move(public_path('job/img'), $file_name_company_logo);
            $imagePathCompanyLogo = "job/img" . $file_name_company_logo;
        }
        $data['company_logo'] =  $imagePathCompanyLogo;
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
        if (request()->hasFile('files')) {
            foreach (request()->file('files') as $key => $item) {
                $pdf = $data['files'][$key];
                $pdfType = $pdf->getClientOriginalExtension();
                $mimeType = $pdf->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_job.' . $pdf->getClientOriginalExtension();
                $pdf->move(public_path('job/files/'), $file_name);
                $pdfPath = "job/files/" . $file_name;
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
        if ($job->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الوظيفية ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'description' => 'nullable',
            'title' => 'nullable',
            'company_name' => 'nullable|string',
            'company_location' => 'nullable|string|location',
            'company_type' => 'nullable|string',
            'company_link' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'job_type' => 'nullable|string',
            'job_duration' => 'nullable',
            'price' => 'nullable|numeric',
            'job_status' => 'nullable|boolean',
            'images_or_video.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'company_region_id' => 'nullable|exists:regions,id',
            'company_city_id' => 'nullable|exists:cities,id',
            'company_neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'deleted_images_and_videos' => 'nullable',
            'delete_files' => 'nullable',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $deletedImagesAndVideos = $data['deleted_images_and_videos'] ?? [];
        foreach ($deletedImagesAndVideos as $imageId) {
            $image = Image::find($imageId);
            if ($image) {
                // Delete from storage
                Storage::delete($image->url);
                // Delete from database
                $image->delete();
            }
        }
        // Handle deleted files
        $deletedFiles = $data['delete_files'] ?? [];
        foreach ($deletedFiles as $fileId) {
            $filePdf = FilePdf::find($fileId);
            if ($filePdf) {
                // Delete from storage
                Storage::delete($filePdf->url);
                // Delete from database
                $filePdf->delete();
            }
        }
        $job->update($data);

        // Handle deleted images and videos        
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
        if (request()->hasFile('files')) {
            foreach (request()->file('files') as $key => $item) {
                $pdf = $data['files'][$key];
                $pdfType = $pdf->getClientOriginalExtension();
                $mimeType = $pdf->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_job.' . $pdf->getClientOriginalExtension();
                $pdf->move(public_path('job/files/'), $file_name);
                $pdfPath = "job/files/" . $file_name;
                $pdfObject = new FilePdf([
                    'url' => $pdfPath,
                    'mime' => $mimeType,
                    'type' => $pdfType,
                ]);
                $job->pdfs()->save($pdfObject);
            }
        }
        return response()->json([
            'message' => 'تم تحديث الوظيفة بنجاح',
            'data' => new JobResource($job),
        ]);
    }

    public function deleteJob(Job $job)
    {
        if ($job->user_id != Auth::id()) {
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
            $fields = ['description', 'title', 'company_name', 'job_type', 'job_duration', 'price'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();
        return JobResource::collection($jobs);
    }
}
