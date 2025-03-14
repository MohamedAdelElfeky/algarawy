<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Meeting;
use Illuminate\Pagination\LengthAwarePaginator;

interface MeetingRepositoryInterface
{
    public function get(int $perPage, int $page): LengthAwarePaginator;
    public function findById(int $id): ?Meeting;
    public function create(array $data): Meeting;
    public function update(Meeting $project, array $data): bool;
    public function delete(Meeting $project): bool;
    public function search(string $searchTerm);
    public function paginate(int $perPage);
}
