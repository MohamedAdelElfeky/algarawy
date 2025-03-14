<?php

namespace App\Infrastructure;

use App\Domain\Models\Project;
use App\Domain\Repositories\ProjectRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use App\Filters\ApprovalStatusFilter;
use App\Filters\BlockedUsersFilter;
use App\Filters\DescriptionFilter;
use App\Filters\NoComplaintsFilter;
use App\Filters\VisibilityStatusFilter;

class EloquentProjectRepository implements ProjectRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator
    {
        $query = Project::query();
        return app(Pipeline::class)
            ->send($query)
            ->through([
                ApprovalStatusFilter::class,
                BlockedUsersFilter::class,
                NoComplaintsFilter::class,
                VisibilityStatusFilter::class,
                DescriptionFilter::class,
            ])
            ->thenReturn()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function findById(int $id): ?Project
    {
        return Project::findOrFail($id);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(Project $project, array $data): bool
    {
        return $project->update($data);
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }

    public function search(string $searchTerm)
    {
        return Project::where('description', 'like', '%' . $searchTerm . '%')->get();
    }

    public function paginate(int $perPage)
    {
        return Project::with(['images', 'pdfs', 'favorites', 'likes'])->paginate($perPage);
    }
}
