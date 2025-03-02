<?php

namespace App\Services;

use App\Domain\Models\Job;
use App\Domain\Models\JobCompanies;
use App\Http\Resources\JobResource;
use App\Models\FilePdf;
use App\Models\Image;
use Illuminate\Http\Request;
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
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $showNoComplaintedPosts = $user->userSettings()
            ->whereHas('setting', function ($query) {
                $query->where('key', 'show_no_complaints_posts');
            })
            ->value('value') ?? false;

        $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id')->toArray();

        $jobQuery = Job::whereNotIn('user_id', $blockedUserIds)
            ->orderBy('created_at', 'desc');

        if ($showNoComplaintedPosts) {
            $jobQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereDoesntHave('complaints');
            });
        }

        $jobs = $jobQuery->paginate($perPage, ['*'], 'page', $page);
        $jobCollection = JobResource::collection($jobs);
        $paginationData = $this->paginationService->getPaginationData($jobCollection);

        return [
            'data' => $jobCollection,
            'metadata' => $paginationData,
        ];
    }

    public function getAllJobsPublic($perPage = 10, $page = 1)
    {

        $jobQuery = Job::where('status', 'public')->orderBy('created_at', 'desc');
        $jobs = $jobQuery->paginate($perPage, ['*'], 'page', $page);
        $jobCollection = JobResource::collection($jobs);
        $paginationData = $this->paginationService->getPaginationData($jobCollection);
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

    public function createJob(array $data, Request $request)
    {
        $data['user_id'] = Auth::id();

        $imagePathCompanyLogo = "";
        if (request()->hasFile('company_logo')) {
            $imageCompanyLogo = request()->file('company_logo');
            $file_name_company_logo = time() . rand(0, 9999999999999) . '_company_logo.' . $imageCompanyLogo->getClientOriginalExtension();
            $imageCompanyLogo->move(public_path('job/img/'), $file_name_company_logo);
            $imagePathCompanyLogo = "job/img/" . $file_name_company_logo;
        }

        // Create Job Entry
        $jobData = [
            'description' => $data['description'] ?? null,
            'title' => $data['title'] ?? null,
            'type' => $data['job_type'] ?? null,
            'duration' => $data['job_duration'] ?? null,
            'is_training' => $data['is_training'] ?? null,
            'price' => $data['price'] ?? null,
            'job_status' => $data['job_status'] ?? null,
            'user_id' => $data['user_id'],
            'region_id' => $data['region_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'neighborhood_id' => $data['neighborhood_id'] ?? null,
            'status' => $data['status'] ?? null,
        ];
        $job = Job::create($jobData);

        // Create Job Company Entry
        if (!empty($data['company_name'])) {
            $jobCompanyData = [
                'job_id' => $job->id,
                'name' => $data['company_name'],
                'location' => $data['company_location'] ?? null,
                'description' => $data['description'] ?? null,
                'type' => $data['company_type'] ?? null,
                'link' => $data['company_link'] ?? null,
                'region_id' => $data['company_region_id'] ?? null,
                'city_id' => $data['company_city_id'] ?? null,
                'neighborhood_id' => $data['company_neighborhood_id'] ?? null,
            ];
            $jobCompany = JobCompanies::create($jobCompanyData);

            // Save Company Logo in JobCompanies
            if ($imagePathCompanyLogo) {
                $jobCompany->images()->create([
                    'url' => $imagePathCompanyLogo,
                    'mime' => 'image/jpeg',
                    'image_type' => 'company_logo',
                ]);
            }
        }

        // Handle Images and Videos
        if (request()->hasFile('images_or_video')) {
            foreach (request()->file('images_or_video') as $item) {
                $file_name = time() . rand(0, 9999999999999) . '_job.' . $item->getClientOriginalExtension();
                $item->move(public_path('job/images/'), $file_name);
                $imagePath = "job/images/" . $file_name;
                $job->images()->create([
                    'url' => $imagePath,
                    'mime' => $item->getMimeType(),
                    'image_type' => $item->getClientOriginalExtension(),
                ]);
            }
        }

        // Handle PDF and Other Files
        if (request()->hasFile('files')) {
            foreach (request()->file('files') as $item) {
                $file_name = time() . rand(0, 9999999999999) . '_job.' . $item->getClientOriginalExtension();
                $item->move(public_path('job/files/'), $file_name);
                $pdfPath = "job/files/" . $file_name;
                $job->pdfs()->create([
                    'url' => $pdfPath,
                    'mime' => $item->getMimeType(),
                    'type' => $item->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json([
            'message' => 'تم إنشاء الوظيفة بنجاح',
            'data' => new JobResource($job),
        ]);
    }

    public function updateJob(Job $job, array $data)
    {
        if ($job->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الوظيفية ليس من إنشائك',
            ], 403);
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
            'files.*' => 'nullable|file',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'company_region_id' => 'nullable|exists:regions,id',
            'company_city_id' => 'nullable|exists:cities,id',
            'company_neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'deleted_images_and_videos' => 'nullable',
            'deleted_files' => 'nullable',
            'deleted_company_logo' => 'nullable',
            'status' => 'nullable',

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
        $deletedFiles = $data['deleted_files'] ?? [];
        foreach ($deletedFiles as $fileId) {
            $filePdf = FilePdf::find($fileId);
            if ($filePdf) {
                // Delete from storage
                Storage::delete($filePdf->url);
                // Delete from database
                $filePdf->delete();
            }
        }
        // dd($data);
        if (!empty($data['deleted_company_logo'])) {
            $oldLogoPath = public_path($job->company_logo);
            if (file_exists($oldLogoPath)) {
                unlink($oldLogoPath);
            }
            $data['company_logo'] = null;
        }

        if (request()->hasFile('company_logo')) {
            $imageCompanyLogo = request()->file('company_logo');
            $file_name_company_logo = time() . rand(0, 9999999999999) . '_company_logo.' . $imageCompanyLogo->getClientOriginalExtension();
            $imageCompanyLogo->move(public_path('job/img/'), $file_name_company_logo);
            $imagePathCompanyLogo = "job/img/" . $file_name_company_logo;
            $data['company_logo'] =  $imagePathCompanyLogo;
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
