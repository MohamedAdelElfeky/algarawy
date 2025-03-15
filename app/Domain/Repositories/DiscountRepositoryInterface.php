<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Discount;
use Illuminate\Pagination\LengthAwarePaginator;

interface DiscountRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator;
    public function findById(int $id): ?Discount;
    public function create(array $data): Discount;
    public function update(Discount $project, array $data): bool;
    public function delete(Discount $project): bool;
    public function search(string $searchTerm);
    public function paginate(int $perPage);
    public function count(): int;
}
