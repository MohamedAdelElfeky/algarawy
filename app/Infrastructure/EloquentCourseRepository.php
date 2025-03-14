<?php

namespace App\Infrastructure;

use App\Domain\Models\Course;
use App\Domain\Repositories\CourseRepositoryInterface;
use App\Filters\ApprovalStatusFilter;
use App\Filters\BlockedUsersFilter;
use App\Filters\DescriptionFilter;
use App\Filters\NoComplaintsFilter;
use App\Filters\VisibilityStatusFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class EloquentCourseRepository implements CourseRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator
    {

        $query = Course::query();
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

    public function findById(int $id): ?Course
    {
        return Course::findOrFail($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(Course $Course, array $data): bool
    {
        return $Course->update($data);
    }

    public function delete(Course $Course): bool
    {
        return $Course->delete();
    }

    public function search(string $searchTerm)
    {
        return Course::where('description', 'like', '%' . $searchTerm . '%')->get();
    }

    public function paginate(int $perPage)
    {
        return Course::with(['images', 'pdfs', 'favorites', 'likes'])->paginate($perPage);
    }
}
