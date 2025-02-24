<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Project;

interface ProjectRepository
{
    public function getAllProjects(int $perPage, int $page);
    public function getProjectById(int $id);
    public function createProject(array $data);
    public function updateProject(Project $project, array $data);
    public function deleteProject(int $id);
}
