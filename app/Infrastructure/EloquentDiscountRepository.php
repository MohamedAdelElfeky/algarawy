<?php

namespace App\Infrastructure;

use App\Domain\Models\Discount;
use App\Domain\Repositories\DiscountRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use App\Filters\ApprovalStatusFilter;
use App\Filters\BlockedUsersFilter;
use App\Filters\DescriptionFilter;
use App\Filters\NoComplaintsFilter;
use App\Filters\VisibilityStatusFilter;

class EloquentDiscountRepository implements DiscountRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator
    {
        $query = Discount::query();
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

    public function findById(int $id): ?Discount
    {
        return Discount::findOrFail($id);
    }

    public function create(array $data): Discount
    {
        return Discount::create($data);
    }

    public function update(Discount $Discount, array $data): bool
    {
        return $Discount->update($data);
    }

    public function delete(Discount $Discount): bool
    {
        return $Discount->delete();
    }

    public function search(string $searchTerm)
    {
        return Discount::where('description', 'like', '%' . $searchTerm . '%')->get();
    }

    public function paginate(int $perPage)
    {
        return Discount::with(['images', 'pdfs', 'favorites', 'likes'])->paginate($perPage);
    }
}
