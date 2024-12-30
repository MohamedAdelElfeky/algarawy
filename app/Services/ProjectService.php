<?php

namespace App\Services;

use App\Http\Resources\ProjectResource;
use App\Models\FilePdf;
use App\Models\Image;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProjectService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }


    public function getAllProjects($perPage = 10, $page = 1)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $showNoComplaintedPosts = $user->show_no_complainted_posts == 1;

        $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id')->toArray();

        $projectQuery = Project::whereNotIn('user_id', $blockedUserIds)
            ->orderBy('created_at', 'desc');

        if ($showNoComplaintedPosts) {
            $projectQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereDoesntHave('complaints'); 
            });
        } else {
            $projectQuery; //->whereHas('complaints'); 
        }
        $projects = $projectQuery->paginate($perPage, ['*'], 'page', $page);
        $projectResource = ProjectResource::collection($projects);
        $paginationData = $this->paginationService->getPaginationData($projects);

        return [
            'data' => $projectResource,
            'metadata' => $paginationData,
        ];
    }
    public function getAllProjectsPublic($perPage = 10, $page = 1)
    {
        $projectQuery = Project::where('status', 'public')->orderBy('created_at', 'desc');
        $projects = $projectQuery->paginate($perPage, ['*'], 'page', $page);
        $projectResource = ProjectResource::collection($projects);
        $paginationData = $this->paginationService->getPaginationData($projects);

        return [
            'data' => $projectResource,
            'metadata' => $paginationData,
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

    public function createProject(array $data)
    {
        $validator = Validator::make($data, [
            'description' => 'nullable|string',
            'images_or_video.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4',
            'files*' => 'nullable|file',
            'location' => 'nullable|string|location',
            'status' => 'nullable',

        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $project = Project::create($data);
        // Handle images/videos
        if (request()->hasFile('images_or_video')) {
            foreach (request()->file('images_or_video') as $key => $item) {
                $image = $data['images_or_video'][$key];
                $imageType = $image->getClientOriginalExtension();
                $mimeType = $image->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_project.' . $image->getClientOriginalExtension();
                $image->move(public_path('project/images/'), $file_name);
                $imagePath = "project/images/" . $file_name;
                $imageObject = new Image([
                    'url' => $imagePath,
                    'mime' => $mimeType,
                    'image_type' => $imageType,
                ]);
                $project->images()->save($imageObject);
            }
        }

        // Handle images/videos
        if (request()->hasFile('files')) {
            foreach (request()->file('files') as $key => $item) {
                $pdf = $data['files'][$key];
                $pdfType = $pdf->getClientOriginalExtension();
                $mimeType = $pdf->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_project.' . $pdf->getClientOriginalExtension();
                $pdf->move(public_path('project/files/'), $file_name);
                $pdfPath = "project/files/" . $file_name;
                $pdfObject = new FilePdf([
                    'url' => $pdfPath,
                    'mime' => $mimeType,
                    'type' => $pdfType,
                ]);
                $project->pdfs()->save($pdfObject);
            }
        }

        return [
            'message' => 'تم إنشاء المشروع بنجاح',
            'data' => new ProjectResource($project),
        ];
    }

    public function updateProject(Project $project, array $data)
    {
        if ($project->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا المشروع ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'description' => 'nullable|string',
            'images_or_video.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4',
            'files*' => 'nullable|file',
            'location' => 'nullable|string|location',
            'deleted_images_and_videos' => 'nullable',
            'deleted_files' => 'nullable',
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
        $project->update($data);
        // Handle images/videos
        if (request()->hasFile('images_or_video')) {
            foreach (request()->file('images_or_video') as $key => $item) {
                $image = $data['images_or_video'][$key];
                $imageType = $image->getClientOriginalExtension();
                $mimeType = $image->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_project.' . $image->getClientOriginalExtension();
                $image->move(public_path('project/images/'), $file_name);
                $imagePath = "project/images/" . $file_name;
                $imageObject = new Image([
                    'url' => $imagePath,
                    'mime' => $mimeType,
                    'image_type' => $imageType,
                ]);
                $project->images()->save($imageObject);
            }
        }

        // Handle images/videos
        if (request()->hasFile('files')) {
            foreach (request()->file('files') as $key => $item) {
                $pdf = $data['files'][$key];
                $pdfType = $pdf->getClientOriginalExtension();
                $mimeType = $pdf->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_project.' . $pdf->getClientOriginalExtension();
                $pdf->move(public_path('project/files/'), $file_name);
                $pdfPath = "project/files/" . $file_name;
                $pdfObject = new FilePdf([
                    'url' => $pdfPath,
                    'mime' => $mimeType,
                    'type' => $pdfType,
                ]);
                $project->pdfs()->save($pdfObject);
            }
        }
        return [
            'message' => 'تم تحديث المشروع بنجاح',
            'data' => new ProjectResource($project),
        ];
        return new ProjectResource($project);
    }

    public function deleteProject($id)
    {
        $project = $this->getProjectById($id);

        if ($project->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا المشروع ليس من إنشائك',
            ], 200);
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
