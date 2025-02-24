<?php

namespace App\Infrastructure;

use App\Domain\Repositories\ProjectRepository;

class EloquentProjectRepository implements ProjectRepository
{
    public function getAllProjects(int $perPage, int $page)
    {
        return Project::paginate($perPage, ['*'], 'page', $page);
    }

    public function getProjectById(int $id)
    {
        return Project::findOrFail($id);
    }

    public function createProject(array $data)
    {
        return Project::create($data);
    }

    public function updateProject(Project $project, array $data)
    {
        return $project->update($data);
    }

    public function deleteProject(int $id)
    {
        return Project::destroy($id);
    }
}
