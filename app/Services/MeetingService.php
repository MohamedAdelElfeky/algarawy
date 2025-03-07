<?php

namespace App\Services;

use App\Domain\Models\Meeting;
use App\Http\Resources\MeetingResource;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MeetingService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function createMeeting(array $data)
    {

        $validator = Validator::make($data, [
            'datetime' => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'link' => 'nullable|string',
            'name' => 'nullable|string',
            'start_time' => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'description' => 'nullable|string',
            'type' => 'nullable|in:remotely,normal',
            'status' => 'nullable',

        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        // $data['startTime'] = date('Y-m-d H:i:s', strtotime($data['start_time']));
        // $data['endTime'] = date('Y-m-d H:i:s', strtotime($data['end_time']));
        // $data['datetime'] = date('Y-m-d H:i:s', strtotime($data['end_time']));
        $meeting = Meeting::create($data);
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

        // Notification::notifiable($users);
        return [
            'message' => 'تم إنشاء الاجتماع بنجاح',
            'data' => new MeetingResource($meeting),
        ];
    }


    public function updateMeeting(Meeting $meeting, array $data)
    {
        if ($meeting->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الاجتماع ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'datetime' => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'link' => 'nullable|string',
            'name' => 'nullable|string',
            'start_time' => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'description' => 'nullable|string',
            'type' => 'nullable|in:remotely,normal',
            'status' => 'nullable',

        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting->update($data);
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
        if ($meeting->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الاجتماع ليس من إنشائك',
            ], 200);
        }

        $meeting->delete();
    }

    public function getMeeting(string $id)
    {
        return Meeting::findOrFail($id);
    }

    public function getAllMeetings($perPage = 10, $page = 1)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $showNoComplaintedPosts = $user->userSettings()
            ->whereHas('setting', function ($query) {
                $query->where('key', 'show_no_complaints_posts');
            })
            ->value('value') ?? false;

        $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id')->toArray();
        $meetingQuery = Meeting::whereNotIn('user_id', $blockedUserIds)->ApprovalStatus('approved')
            ->orderBy('created_at', 'desc');
        if ($showNoComplaintedPosts) {
            $meetingQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereDoesntHave('complaints');
            });
        }
        $meetings = $meetingQuery->paginate($perPage, ['*'], 'page', $page);
        $meetingResource =  MeetingResource::collection($meetings);
        $paginationData = $this->paginationService->getPaginationData($meetings);

        return  [
            'data' => $meetingResource,
            'metadata' => $paginationData,
        ];
    }

    public function getAllMeetingsPublic($perPage = 10, $page = 1)
    {
        $meetingQuery = Meeting::visibilityStatus('public')->ApprovalStatus('approved')
            ->orderBy('created_at', 'desc');
        $meetings = $meetingQuery->paginate($perPage, ['*'], 'page', $page);
        $meetingResource =  MeetingResource::collection($meetings);
        $paginationData = $this->paginationService->getPaginationData($meetings);

        return  [
            'data' => $meetingResource,
            'metadata' => $paginationData,
        ];
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
