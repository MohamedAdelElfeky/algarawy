<?php

namespace App\Infrastructure;

use App\Domain\Models\Meeting;
use App\Domain\Repositories\MeetingRepositoryInterface;

class EloquentMeetingRepository implements MeetingRepositoryInterface
{
    public function create(array $data): Meeting
    {
        return Meeting::create($data);
    }

    public function update(Meeting $meeting, array $data): Meeting
    {
        $meeting->update($data);
        return $meeting;
    }

    public function delete(Meeting $meeting): void
    {
        $meeting->delete();
    }

    public function findById(string $id): ?Meeting
    {
        return Meeting::find($id);
    }

    public function getAll(int $perPage, int $page)
    {
        return Meeting::orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
    }
}
