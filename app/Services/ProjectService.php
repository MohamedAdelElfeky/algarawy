<?php

namespace App\Services;

use App\Http\Resources\ProjectResource;
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
            'description' => 'required|string',
            'images_or_videos' => 'nullable|file',
            'files_pdf' => 'nullable|file',
            'location' => ['string', function ($attribute, $value, $fail) {
                if (!preg_match('/^https:\/\/www\.google\.com\/maps\/.*$/', $value)) {
                    $fail($attribute . ' must be a valid Google Maps link.');
                }
            }],
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
        if (request()->hasFile('images_or_videos')) {  //dd(request()->file('images_or_videos'));
            foreach (request()->file('images_or_videos') as $image) {
                $imagePath = $image->store('images_or_videos', 'public');dd($imagePath); // Store the image and get the path
                $project->imagesOrVideos()->create(['url' => $imagePath]);
            }
        }

        // Handle PDF files
        if (request()->hasFile('files_pdf')) {
            foreach (request()->file('files_pdf') as $pdf) {
                $pdfPath = $pdf->store('files_pdf', 'public'); // Store the PDF file and get the path
                $project->filesPdf()->create(['url' => $pdfPath]);
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
        return Project::where(function ($query) use ($searchTerm) {
            $fields = ['description'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();
    }
}
