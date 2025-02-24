<?php
namespace App\Domain\Repositories;

use App\Domain\Models\Meeting;

interface MeetingRepositoryInterface
{
    public function create(array $data): Meeting;
    public function update(Meeting $meeting, array $data): Meeting;
    public function delete(Meeting $meeting): void;
    public function findById(string $id): ?Meeting;
    public function getAll(int $perPage, int $page);
}
