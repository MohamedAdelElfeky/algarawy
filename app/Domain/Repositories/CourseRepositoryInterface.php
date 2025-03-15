<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;

interface CourseRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator;
    public function findById(int $id): ?Course;
    public function create(array $data): Course;
    public function update(Course $project, array $data): bool;
    public function delete(Course $project): bool;
    public function search(string $searchTerm);
    public function paginate(int $perPage);
    public function count(): int;
}
