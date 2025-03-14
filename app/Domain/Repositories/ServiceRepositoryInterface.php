<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator;
    public function findById(int $id): ?Service;
    public function create(array $data): Service;
    public function update(Service $project, array $data): bool;
    public function delete(Service $project): bool;
    public function search(string $searchTerm);
    public function paginate(int $perPage);
}
