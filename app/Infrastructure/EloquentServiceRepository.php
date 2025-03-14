<?php

namespace App\Infrastructure;

use App\Domain\Models\Service;
use App\Domain\Repositories\ServiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use App\Filters\ApprovalStatusFilter;
use App\Filters\BlockedUsersFilter;
use App\Filters\DescriptionFilter;
use App\Filters\NoComplaintsFilter;
use App\Filters\VisibilityStatusFilter;

class EloquentServiceRepository implements ServiceRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator
    {
        $query = Service::query();
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

    public function findById(int $id): ?Service
    {
        return Service::findOrFail($id);
    }

    public function create(array $data): Service
    {
        return Service::create($data);
    }

    public function update(Service $Service, array $data): bool
    {
        return $Service->update($data);
    }

    public function delete(Service $Service): bool
    {
        return $Service->delete();
    }

    public function search(string $searchTerm)
    {
        return Service::where('description', 'like', '%' . $searchTerm . '%')->get();
    }

    public function paginate(int $perPage)
    {
        return Service::with(['images', 'pdfs', 'favorites', 'likes'])->paginate($perPage);
    }
}
