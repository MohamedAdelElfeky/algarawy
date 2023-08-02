<?php

namespace App\Services;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ProjectService
{
    public function getAllProjects()
    {
        $projects = Project::all();
        return ProjectResource::collection($projects);
    }

    public function getProjectById($id)
    {
        $project = Project::findOrFail($id);
        if (!$project) {
            abort(404, 'Project not found');
        }
        return $project;
    }

    public function createProject(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'images_or_videos' => 'nullable|array',
            'files_pdf' => 'nullable|array',
            'location' => 'nullable|string|max:255',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $project = Project::create($data);
        return[
            'message' => 'Project created successfully',
            'data' => new ProjectResource($project),
        ];
    }

    public function updateProject(Project $project, array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'images_or_videos' => 'nullable|array',
            'files_pdf' => 'nullable|array',
            'location' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = Auth::id();
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $project->update($data);

        return [
            'message' => 'Project updated successfully',
            'data' => new ProjectResource($project),
        ];
        return new ProjectResource($project);
    }

    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return new ProjectResource($project);
    }
}
