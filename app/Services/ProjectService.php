<?php

namespace App\Services;

use App\Domain\Models\FilePdf;
use App\Domain\Models\Image;
use App\Domain\Models\Project;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProjectService
{

    public function __construct(private PaginationService $paginationService, private FileHandlerService $fileHandler) {}
    public function getProjects($perPage = 10, $page = 1)
    {
        $user = Auth::guard('sanctum')->user();
        $projectQuery = Project::query()->approvalStatus('approved')->orderByDesc('created_at');

        if ($user) {
            $showNoComplaintedPosts = $user->userSettings()
                ->whereHas('setting', fn($query) => $query->where('key', 'show_no_complaints_posts'))
                ->value('value') ?? false;

            $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id');

            $projectQuery->whereNotIn('user_id', $blockedUserIds);

            if ($showNoComplaintedPosts) {
                $projectQuery->where(
                    fn($query) =>
                    $query->where('user_id', $user->id)
                        ->orWhereDoesntHave('complaints')
                );
            }
        } else {
            $projectQuery->visibilityStatus();
        }

        $projects = $projectQuery->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => ProjectResource::collection($projects),
            'metadata' => $this->paginationService->getPaginationData($projects),
        ];
    }

    public function getProjectById($id)
    {
        $project = Project::findOrFail($id);
        if (!$project) {
            abort(404, 'المشروع غير موجود');
        }
        return $project;
    }

    public function createProject(ProjectRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $project = Project::create($validatedData);
        $project->Approval()->create([
            'status' => 'pending'
        ]);
        $project->visibility()->create([
            'status' => 'private'
        ]);
        $this->fileHandler->attachImages(request(), $project, 'project/images', 'project_');
        $this->fileHandler->attachPdfs(request(), $project, 'project/pdf', 'pdf_');

        return [
            'message' => 'تم إنشاء المشروع بنجاح',
            'data' => new ProjectResource($project),
        ];
    }

    public function updateProject(Project $project, ProjectRequest $request)
    {
        if (!$project->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا المشروع ليس من إنشائك',
            ], 403);
        }
    
        $validatedData = $request->validated();
    
        if ($request->filled('deleted_images_and_videos')) {
            $this->fileHandler->deleteFiles($request->deleted_images_and_videos, 'image');
        }
        if ($request->filled('deleted_files')) {
            $this->fileHandler->deleteFiles($request->deleted_files, 'pdf');
        }
    
        $project->update($validatedData);
      
        $this->fileHandler->attachImages($request, $project, 'project/images', 'project_');
        $this->fileHandler->attachPdfs($request, $project, 'project/pdf', 'pdf_');
    
        return response()->json([
            'message' => 'تم تحديث المشروع بنجاح',
            'data' => new ProjectResource($project),
        ]);
    }
    

    public function deleteProject($id)
    {
        $project = $this->getProjectById($id);

        if (!$project->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا المشروع ليس من إنشائك',
            ], 403);
        }

        $project->delete();
        return new ProjectResource($project);
    }

    public function searchProject($searchTerm)
    {
        $projects = Project::where(function ($query) use ($searchTerm) {
            $fields = ['description'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();

        return ProjectResource::collection($projects);
    }
}
