<?php

namespace App\Infrastructure;

use App\Domain\Models\Job;
use App\Domain\Repositories\JobRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use App\Filters\ApprovalStatusFilter;
use App\Filters\BlockedUsersFilter;
use App\Filters\DescriptionFilter;
use App\Filters\NoComplaintsFilter;
use App\Filters\VisibilityStatusFilter;

class EloquentJobRepository implements JobRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator
    {
        $query = Job::query();
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

    public function findById(int $id): ?Job
    {
        return Job::findOrFail($id);
    }

    public function create(array $data): Job
    {
        return Job::create($data);
    }

    public function update(Job $Job, array $data): bool
    {
        return $Job->update($data);
    }

    public function delete(Job $Job): bool
    {
        return $Job->delete();
    }

    public function search(string $searchTerm)
    {
        return Job::where('description', 'like', '%' . $searchTerm . '%')->get();
    }

    public function paginate(int $perPage)
    {
        return Job::with(['images', 'pdfs', 'favorites', 'likes', 'JobCompanies'])->paginate($perPage);
    }
}
