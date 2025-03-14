<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Job;
use Illuminate\Pagination\LengthAwarePaginator;

interface JobRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator;
    public function findById(int $id): ?Job;
    public function create(array $data): Job;
    public function update(Job $project, array $data): bool;
    public function delete(Job $project): bool;
    public function search(string $searchTerm);
    public function paginate(int $perPage);
}
