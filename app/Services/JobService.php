<?php

namespace App\Services;

use App\Domain\Models\Job;
use App\Domain\Models\JobCompanies;
use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobService
{

    public function __construct(private PaginationService $paginationService, private FileHandlerService $fileHandler) {}

    public function getJobs($perPage = 10, $page = 1)
    {
        $user = Auth::guard('sanctum')->user();
        $jobQuery = Job::query()->approvalStatus('approved')->orderByDesc('created_at');
        if ($user) {
            $showNoComplaintedPosts = $user->userSettings()
                ->whereHas('setting', fn($query) => $query->where('key', 'show_no_complaints_posts'))
                ->value('value') ?? false;

            $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id');

            $jobQuery->whereNotIn('user_id', $blockedUserIds);

            if ($showNoComplaintedPosts) {
                $jobQuery->where(
                    fn($query) =>
                    $query->where('user_id', $user->id)
                        ->orWhereDoesntHave('complaints')
                );
            }
        } else {
            $jobQuery->visibilityStatus();
        }

        $jobs = $jobQuery->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => JobResource::collection($jobs),
            'metadata' => $this->paginationService->getPaginationData($jobs),
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

    public function createJob(JobRequest $request)
    {
        $validatedData = $request->validated();
        $job = Job::create([
            'user_id' => auth()->id(),
            'description' => $validatedData['description']?? null,
            'title' => $validatedData['title']?? null,
            'type' => $validatedData['job_type']?? null,
            'duration' => $validatedData['job_duration']?? null,
            'is_training' => $validatedData['is_training'] ?? null,
            'price' => $validatedData['price'] ?? null,
            'job_status' => $validatedData['job_status']?? null,
            'region_id' => $validatedData['region_id']?? null,
            'city_id' => $validatedData['city_id']?? null,
            'neighborhood_id' => $validatedData['neighborhood_id']?? null,
        ]);
        $job->Approval()->create([
            'status' => 'pending'
        ]);
        $job->visibility()->create([
            'status' => 'private'
        ]);
        $this->fileHandler->attachImages(request(), $job, 'job/images', 'project_');
        $this->fileHandler->attachPdfs(request(), $job, 'job/pdf', 'pdf_');
        if (!empty($validatedData['company_name'])) {
            $jobCompanyData = [
                'job_id' => $job->id,
                'name' => $validatedData['company_name'],
                'location' => $validatedData['company_location'] ?? null,
                'description' => $validatedData['description'] ?? null,
                'type' => $validatedData['company_type'] ?? null,
                'link' => $validatedData['company_link'] ?? null,
                'region_id' => $validatedData['company_region_id'] ?? null,
                'city_id' => $validatedData['company_city_id'] ?? null,
                'neighborhood_id' => $validatedData['company_neighborhood_id'] ?? null,
            ];
            $jobCompany = JobCompanies::create($jobCompanyData);
            $this->fileHandler->uploadSingleImage($request, $jobCompany, 'users', 'user', 'image', 'company_logo');
        }

        return response()->json([
            'message' => 'تم إنشاء الوظيفة بنجاح',
            'data' => new JobResource($job),
        ]);
    }

    public function updateJob(Job $job, JobRequest $request)
    {
        if (!$job->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا الوظيفة ليس من إنشائك',
            ], 403);
        }

        $validatedData = $request->validated();

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
        ]);
        $this->fileHandler->attachImages(request(), $job, 'job/images', 'project_');
        $this->fileHandler->attachPdfs(request(), $job, 'job/pdf', 'pdf_');
        // Update Job Company Entry
        if (!empty($validatedData['company_name'])) {
            $jobCompany = JobCompanies::updateOrCreate(
                ['job_id' => $job->id],
                [
                    'name' => $validatedData['company_name'],
                    'location' => $validatedData['company_location'] ?? null,
                    'description' => $validatedData['description'] ?? null,
                    'type' => $validatedData['company_type'] ?? null,
                    'link' => $validatedData['company_link'] ?? null,
                    'region_id' => $validatedData['company_region_id'] ?? null,
                    'city_id' => $validatedData['company_city_id'] ?? null,
                    'neighborhood_id' => $validatedData['company_neighborhood_id'] ?? null,
                ]
            );
            $this->fileHandler->uploadSingleImage($request, $jobCompany, 'users', 'user', 'image', 'company_logo');
        }
       

        return response()->json([
            'message' => 'تم تحديث الوظيفة بنجاح',
            'data' => new JobResource($job),
        ]);
    }
    public function deleteJob(Job $job)
    {
        if (!$job->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا الوظيفة ليس من إنشائك',
            ], 403);
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
