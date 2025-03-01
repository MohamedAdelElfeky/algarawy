<?php

namespace App\Domain\Services;

use App\Domain\Models\Meeting;
use App\Domain\Repositories\MeetingRepositoryInterface;
use App\Http\Resources\MeetingResource;
use Illuminate\Support\Facades\Auth;

class MeetingService
{
    private $repository;

    public function __construct(MeetingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    public function createMeeting(array $data)
    {
        $data['user_id'] = Auth::id();
        $meeting = $this->repository->create($data);
        return new MeetingResource($meeting);
    }

    public function updateMeeting(Meeting $meeting, array $data)
    {
        if ($meeting->user_id != Auth::id()) {
            throw new \Exception('هذا الاجتماع ليس من إنشائك');
        }
        $updatedMeeting = $this->repository->update($meeting, $data);
        return new MeetingResource($updatedMeeting);
    }

    public function deleteMeeting(Meeting $meeting)
    {
        if ($meeting->user_id != Auth::id()) {
            throw new \Exception('هذا الاجتماع ليس من إنشائك');
        }
        $this->repository->delete($meeting);
        return response()->json(['message' => 'تم حذف الاجتماع بنجاح'], 200);
    }

    public function getMeeting(string $id)
    {
        $meeting = $this->repository->findById($id);
        return new MeetingResource($meeting);
    }

    public function getAllMeetings(int $perPage, int $page)
    {
        $meetings = $this->repository->getAll($perPage, $page);
        return MeetingResource::collection($meetings);
    }
    
}
