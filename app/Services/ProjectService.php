<?php

namespace App\Services;

use App\Http\Resources\ProjectResource;
use App\Models\FilePdf;
use App\Models\Image;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ProjectService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }


    public function getAllProjects($perPage = 10, $page = 1)
    {
        $projects = Project::paginate($perPage, ['*'], 'page', $page);
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
            'files_pdf.*' => 'nullable|file',
            'location' => 'nullable|string|location',
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
        if (request()->hasFile('files_pdf')) {
            foreach (request()->file('files_pdf') as $key => $item) {
                $pdf = $data['files_pdf'][$key];
                $pdfType = $pdf->getClientOriginalExtension();
                $mimeType = $pdf->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_project.' . $pdf->getClientOriginalExtension();
                $pdf->move(public_path('project/pdf/'), $file_name);
                $pdfPath = "project/pdf/" . $file_name;
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
        if (($project->user_id) != Auth::id()); {
            return response()->json([
                'message' => 'هذا المشروع ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'description' => 'required|string',
            'images_or_videos' => 'nullable|array',
            'files_pdf' => 'nullable|array',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $project->update($data);

        return [
            'message' => 'تم تحديث المشروع بنجاح',
            'data' => new ProjectResource($project),
        ];
        return new ProjectResource($project);
    }

    public function deleteProject($id)
    {
        $project = $this->getProjectById($id);

        if (($project->user_id) != Auth::id()); {
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
