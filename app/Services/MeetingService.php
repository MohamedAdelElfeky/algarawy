<?php

namespace App\Services;

use App\Domain\Models\Meeting;
use App\Http\Requests\MeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MeetingService
{

    public function __construct(private PaginationService $paginationService) {}

    public function getMeetings($perPage = 10, $page = 1)
    {
        $user = Auth::guard('sanctum')->user();
        $meetingQuery = Meeting::query()->approvalStatus('approved')->orderByDesc('created_at');
        if ($user) {
            $showNoComplaintedPosts = $user->userSettings()
                ->whereHas('setting', fn($query) => $query->where('key', 'show_no_complaints_posts'))
                ->value('value') ?? false;

            $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id');

            $meetingQuery->whereNotIn('user_id', $blockedUserIds);

            if ($showNoComplaintedPosts) {
                $meetingQuery->where(
                    fn($query) =>
                    $query->where('user_id', $user->id)
                        ->orWhereDoesntHave('complaints')
                );
            }
        } else {
            $meetingQuery->visibilityStatus();
        }

        $meetings = $meetingQuery->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => MeetingResource::collection($meetings),
            'metadata' => $this->paginationService->getPaginationData($meetings),
        ];
    }


    public function createMeeting(MeetingRequest $request)
    {

        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $meeting = Meeting::create($validatedData);
        $meeting->Approval()->create([
            'status' => 'pending'
        ]);
        $meeting->visibility()->create([
            'status' => 'private'
        ]);
        $users = User::all();
        $meetingDateTime = new DateTime($meeting->datetime);
        foreach ($users as $user) {
            if ($user->id !== Auth::id()) {
                $notificationData = [
                    'user_id' => $user->id,
                    'notifiable_id' => $meeting->id,
                    'title' => 'دعوة',
                    'message'  => ' تمت دعوتك لحضور الاجتماع ' . $meeting->name . ' بتاريخ ' . $meetingDateTime->format('Y-m-d'),
                ];
                $meeting->notifications()->create($notificationData);
            }
        }
        return [
            'message' => 'تم إنشاء الاجتماع بنجاح',
            'data' => new MeetingResource($meeting),
        ];
    }


    public function updateMeeting(Meeting $meeting, MeetingRequest $request)
    {
        if (!$meeting->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا الاجتماع ليس من إنشائك',
            ], 403);
        }

        $validatedData = $request->validated();
        $meeting->update($validatedData);       
        $users = User::all();
        foreach ($users as $user) {
            if ($user->id !== Auth::id()) {
                $notificationData = [
                    'user_id' => $user->id,
                    'notifiable_id' => $meeting->id,
                    'title' => 'دعوة',
                    'message'  => ' تمت تحديث الدعوة لحضور الاجتماع ' . $meeting->name . ' بتاريخ ' . $meeting->datetime,
                ];
                $meeting->notifications()->create($notificationData);
            }
        }
        return [
            'message' => 'تم تحديث الاجتماع بنجاح',
            'data' => new MeetingResource($meeting),
        ];
    }

    public function deleteMeeting(Meeting $meeting)
    {
        if (!$meeting->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا الاجتماع ليس من إنشائك',
            ], 403);
        }

        $meeting->delete();
    }

    public function getMeeting(string $id)
    {
        return Meeting::findOrFail($id);
    }
    
    public function searchMeeting($searchTerm)
    {
        $meetings = Meeting::where(function ($query) use ($searchTerm) {
            $fields = ['description', 'name', 'from', 'to'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();
        return MeetingResource::collection($meetings);
    }
}
