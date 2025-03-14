<?php

namespace App\Domain\Services;

use App\Domain\Models\Project;
use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Shared\Traits\HandlesFileDeletion;
use App\Shared\Traits\HandlesMultipleFileUpload;
use App\Shared\Traits\HandlesMultipleImageUpload;
use App\Shared\Traits\ownershipAuthorization;

class ProjectService
{
    use HandlesMultipleImageUpload,
        HandlesMultipleFileUpload,
        HandlesFileDeletion,
        ownershipAuthorization;

    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private PaginationService $paginationService,
    ) {}

    public function getProjects(int $perPage = 10, int $page = 1): array
    {
        $projects = $this->projectRepository->get($perPage, $page);
        return [
            'data' => ProjectResource::collection($projects),
            'metadata' => $this->paginationService->getPaginationData($projects),
        ];
    }

    public function getProjectById(int $id): ?Project
    {
        return $this->projectRepository->findById($id);
    }

    public function createProject(ProjectRequest $request): array
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $project = $this->projectRepository->create($validatedData);

        $project->Approval()->create(['status' => 'pending']);
        $project->visibility()->create(['status' => 'private']);

        $this->handleFileAttachments($request, $project);

        return [
            'message' => 'تم إنشاء المشروع بنجاح',
            'data' => new ProjectResource($project),
        ];
    }

    public function updateProject(Project $project, ProjectRequest $request): array
    {
        $this->authorizeOwnership($project);

        $validatedData = $request->validated();

        if ($request->filled('deleted_images_and_videos')) {
            $this->deleteFiles($request->deleted_images_and_videos, 'image');
        }
        if ($request->filled('deleted_files')) {
            $this->deleteFiles($request->deleted_files, 'pdf');
        }

        $this->projectRepository->update($project, $validatedData);
        $this->handleFileAttachments($request, $project);

        return [
            'message' => 'تم تحديث المشروع بنجاح',
            'data' => new ProjectResource($project),
        ];
    }

    public function deleteProject(int $id, string $type = 'api'): array
    {
        $project = $this->getProjectById($id);
        $this->authorizeOwnership($project, $type);

        $this->projectRepository->delete($project);

        return [
            'message' => 'تم حذف المشروع بنجاح'
        ];
    }

    public function searchProject(string $searchTerm)
    {
        return ProjectResource::collection($this->projectRepository->search($searchTerm));
    }

    public function getPaginatedProjects(int $perPage)
    {
        return $this->projectRepository->paginate($perPage);
    }

    /**
     * Handle file and image attachments for a project.
     */
    private function handleFileAttachments(ProjectRequest $request, Project $project): void
    {
        $this->attachImages($request, $project, 'project/images', 'project_');
        $this->attachFiles($request, $project, 'project/pdf', 'pdf_');
    }
}
