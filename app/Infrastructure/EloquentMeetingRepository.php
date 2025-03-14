<?php

namespace App\Infrastructure;

use App\Domain\Models\Meeting;
use App\Domain\Repositories\MeetingRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use App\Filters\ApprovalStatusFilter;
use App\Filters\BlockedUsersFilter;
use App\Filters\DescriptionFilter;
use App\Filters\NoComplaintsFilter;
use App\Filters\VisibilityStatusFilter;
class EloquentMeetingRepository implements MeetingRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator
    {
        $query = Meeting::query();
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
            ->paginate($perPage, ['*'], 'page', $page);    }

    public function findById(int $id): ?Meeting
    {
        return Meeting::findOrFail($id);
    }

    public function create(array $data): Meeting
    {
        return Meeting::create($data);
    }

    public function update(Meeting $Meeting, array $data): bool
    {
        return $Meeting->update($data);
    }

    public function delete(Meeting $Meeting): bool
    {
        return $Meeting->delete();
    }

    public function search(string $searchTerm)
    {
        return Meeting::where('description', 'like', '%' . $searchTerm . '%')->get();
    }

    public function paginate(int $perPage)
    {
        return Meeting::with(['images', 'pdfs', 'favorites', 'likes'])->paginate($perPage);
    }
}
