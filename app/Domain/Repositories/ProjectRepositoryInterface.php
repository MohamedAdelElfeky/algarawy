<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator;
    public function findById(int $id): ?Project;
    public function create(array $data): Project;
    public function update(Project $project, array $data): bool;
    public function delete(Project $project): bool;
    public function search(string $searchTerm);
    public function paginate(int $perPage);
    public function count(): int;
}
