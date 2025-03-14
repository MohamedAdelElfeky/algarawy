<?php

namespace App\Domain\Services;

use App\Domain\Models\Job;
use App\Domain\Models\JobCompanies;
use App\Domain\Repositories\JobRepositoryInterface;
use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use App\Shared\Traits\HandlesFileDeletion;
use App\Shared\Traits\HandlesMultipleFileUpload;
use App\Shared\Traits\HandlesMultipleImageUpload;
use App\Shared\Traits\HandlesSingleImageUpload;
use App\Shared\Traits\ownershipAuthorization;
use Illuminate\Support\Facades\Auth;

class JobService
{
    use HandlesMultipleImageUpload,
        HandlesMultipleFileUpload,
        HandlesFileDeletion,
        HandlesSingleImageUpload,
        ownershipAuthorization;

    public function __construct(
        private PaginationService $paginationService,
        private JobRepositoryInterface $jobRepository
    ) {}

    public function getJobs(int $perPage = 10, int $page = 1): array
    {
        $jobs = $this->jobRepository->get($perPage, $page);
        return [
            'data' => JobResource::collection($jobs),
            'metadata' => $this->paginationService->getPaginationData($jobs),
        ];
    }

    public function getJobById(int $id)
    {
        return $this->jobRepository->findById($id);
    }

    public function createJob(JobRequest $request): array
    {
        $validatedData = $request->validated();
        $validatedData['type'] = $validatedData['job_type'] ?? null;
        $validatedData['duration'] = $validatedData['job_duration'] ?? null;
        unset($validatedData['job_type'], $validatedData['job_duration']);
        // $jobData = collect($validatedData)->only([
        //     'description',
        //     'title',
        //     'type',
        //     'duration',
        //     'is_training',
        //     'price',
        //     'job_status',
        //     'user_id',
        //     'region_id',
        //     'city_id',
        //     'neighborhood_id'
        // ])->toArray();
        $job = $this->jobRepository->create($validatedData);
       
        $job->Approval()->create(['status' => 'pending']);
        $job->visibility()->create(['status' => 'private']);

        $this->handleFileAttachments($request, $job);
        $this->handleJobCompany($request, $job, $validatedData);

        return [
            'message' => 'تم إنشاء الوظيفة بنجاح',
            'data' => new JobResource($job),
        ];
    }

    public function updateJob(Job $job, JobRequest $request)
    {

        $this->authorizeOwnership($job);
        $validatedData = $request->validated();
        $validatedData['type'] = $validatedData['job_type'] ?? $job->type;
        $validatedData['duration'] = $validatedData['job_duration'] ?? $job->duration;    
        $jobData = collect($validatedData)->only([
            'description', 'title', 'type', 'duration', 'is_training', 'price',
            'job_status', 'region_id', 'city_id', 'neighborhood_id'
        ])->toArray();
        $job->update($jobData);
        $this->handleFileAttachments($request, $job);
        $this->handleJobCompany($request, $job, $validatedData);

        return [
            'message' => 'تم تحديث الوظيفة بنجاح',
            'data' => new JobResource($job),
        ];
    }

    public function deleteJob(int $id, string $type = 'api'): array
    {
        $job = $this->getJobById($id);
        $this->authorizeOwnership($job, $type);
        $job->delete();
        return ['message' => 'تم حذف الوظيفة بنجاح'];
    }

    public function searchJob(string $searchTerm)
    {
        return JobResource::collection($this->jobRepository->search($searchTerm));
    }

    public function getPaginated(int $perPage)
    {
        return $this->jobRepository->paginate($perPage);
    }

    private function handleFileAttachments(JobRequest $request, Job $job): void
    {
        $this->attachImages($request, $job, 'job/images', 'project_');
        $this->attachFiles($request, $job, 'job/pdf', 'pdf_');
    }

    private function handleJobCompany(JobRequest $request, Job $job, array $validatedData): void
    {
        if (!empty($validatedData['company_name'])) { 
            $jobCompany = JobCompanies::updateOrCreate(
                ['job_id' => $job->id],
                array_filter([
                    'name' => $validatedData['company_name'],
                    'location' => $validatedData['company_location'] ?? null,
                    'description' => $validatedData['company_description'] ?? null,
                    'type' => $validatedData['company_type'] ?? null,
                    'link' => $validatedData['company_link'] ?? null,
                    'region_id' => $validatedData['company_region_id'] ?? null,
                    'city_id' => $validatedData['company_city_id'] ?? null,
                    'neighborhood_id' => $validatedData['company_neighborhood_id'] ?? null,
                ])
            );
            $this->uploadSingleImage($request, $jobCompany, 'users', 'user', 'image', 'company_logo');
        }
    }
}
