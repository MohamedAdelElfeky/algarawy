<?php

namespace App\Services;

use App\Domain\Models\Job;
use App\Domain\Models\JobCompanies;
use App\Http\Resources\JobResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->ApprovalStatus('approved')
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

        $jobQuery = Job::visibilityStatus('public')->ApprovalStatus('approved')
            ->orderBy('created_at', 'desc');
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

    public function updateJob(Job $job, array $data, Request $request)
{
    if ($job->user_id != Auth::id()) {
        return response()->json([
            'message' => 'هذا الوظيفية ليس من إنشائك',
        ], 403);
    }

    $data['user_id'] = Auth::id();
    $imagePathCompanyLogo = "";
    
    if ($request->hasFile('company_logo')) {
        $imageCompanyLogo = $request->file('company_logo');
        $file_name_company_logo = time() . rand(0, 9999999999999) . '_company_logo.' . $imageCompanyLogo->getClientOriginalExtension();
        $imageCompanyLogo->move(public_path('job/img/'), $file_name_company_logo);
        $imagePathCompanyLogo = "job/img/" . $file_name_company_logo;
    }

    // Update Job Entry
    $job->update([
        'description' => $data['description'] ?? $job->description,
        'title' => $data['title'] ?? $job->title,
        'type' => $data['job_type'] ?? $job->type,
        'duration' => $data['job_duration'] ?? $job->duration,
        'is_training' => $data['is_training'] ?? $job->is_training,
        'price' => $data['price'] ?? $job->price,
        'job_status' => $data['job_status'] ?? $job->job_status,
        'region_id' => $data['region_id'] ?? $job->region_id,
        'city_id' => $data['city_id'] ?? $job->city_id,
        'neighborhood_id' => $data['neighborhood_id'] ?? $job->neighborhood_id,
        'status' => $data['status'] ?? $job->status,
    ]);

    // Update Job Company Entry
    if (!empty($data['company_name'])) {
        $jobCompany = JobCompanies::updateOrCreate(
            ['job_id' => $job->id],
            [
                'name' => $data['company_name'],
                'location' => $data['company_location'] ?? null,
                'description' => $data['description'] ?? null,
                'type' => $data['company_type'] ?? null,
                'link' => $data['company_link'] ?? null,
                'region_id' => $data['company_region_id'] ?? null,
                'city_id' => $data['company_city_id'] ?? null,
                'neighborhood_id' => $data['company_neighborhood_id'] ?? null,
            ]
        );
        
        if ($imagePathCompanyLogo) {
            $jobCompany->images()->create([
                'url' => $imagePathCompanyLogo,
                'mime' => 'image/jpeg',
                'image_type' => 'company_logo',
            ]);
        }
    }

    // Handle Images and Videos
    if ($request->hasFile('images_or_video')) {
        foreach ($request->file('images_or_video') as $item) {
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
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $item) {
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
