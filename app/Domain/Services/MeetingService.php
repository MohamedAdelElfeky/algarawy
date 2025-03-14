<?php

namespace App\Domain\Services;

use App\Domain\Models\Meeting;
use App\Domain\Models\User;
use App\Domain\Repositories\MeetingRepositoryInterface;
use App\Http\Requests\MeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Shared\Traits\ownershipAuthorization;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class MeetingService
{
    use ownershipAuthorization;

    public function __construct(
        private MeetingRepositoryInterface $meetingRepository,
        private PaginationService $paginationService
    ) {}

    public function getMeetings(int $perPage = 10, int $page = 1): array
    {
        $meetings = $this->meetingRepository->get($perPage, $page);
        return [
            'data' => MeetingResource::collection($meetings),
            'metadata' => $this->paginationService->getPaginationData($meetings),
        ];
    }
    
    public function getMeetingById(string $id): Meeting
    {
        return Meeting::findOrFail($id);
    }

    public function createMeeting(MeetingRequest $request): array
    {
        $validatedData = $request->validated();
        $meeting = Meeting::create($validatedData);
        $meeting->approval()->create(['status' => 'pending']);
        $meeting->visibility()->create(['status' => 'private']);

        $this->notifyUsersAboutMeeting($meeting, 'دعوة', 'تمت دعوتك لحضور الاجتماع');

        return [
            'message' => 'تم إنشاء الاجتماع بنجاح',
            'data' => new MeetingResource($meeting),
        ];
    }

    public function updateMeeting(Meeting $meeting, MeetingRequest $request)
    {
        $this->authorizeOwnership($meeting);

        $meeting->update($request->validated());

        $this->notifyUsersAboutMeeting($meeting, 'دعوة', 'تم تحديث الدعوة لحضور الاجتماع');

        return [
            'message' => 'تم تحديث الاجتماع بنجاح',
            'data' => new MeetingResource($meeting),
        ];
    }

    public function deleteMeeting(int $id, $type = 'api'): JsonResponse
    {
        $meeting = $this->getMeetingById($id);

        $this->authorizeOwnership($meeting, $type);


        $meeting->delete();
        return response()->json(['message' => 'تم حذف الاجتماع بنجاح']);
    }

 
    public function searchMeeting(string $searchTerm)
    {
        return MeetingResource::collection($this->meetingRepository->search($searchTerm));
    }

    public function getPaginatedMeeting(int $perPage)
    {
        return $this->meetingRepository->paginate($perPage);
    }


    private function notifyUsersAboutMeeting(Meeting $meeting, string $title, string $messagePrefix): void
    {
        $meetingDateTime = new DateTime($meeting->datetime);
        $formattedDate = $meetingDateTime->format('Y-m-d');

        User::where('id', '!=', Auth::id())->each(function (User $user) use ($meeting, $title, $messagePrefix, $formattedDate) {
            $meeting->notifications()->create([
                'user_id' => $user->id,
                'notifiable_id' => $meeting->id,
                'title' => $title,
                'message' => "{$messagePrefix} {$meeting->name} بتاريخ {$formattedDate}",
            ]);
        });
    }
}
