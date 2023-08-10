<?php

namespace App\Services;

use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use App\Models\Notification;
// use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\MeetingNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
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
            'datetime' => 'nullable|date',
            'link' => 'nullable|string',
            'name' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'description' => 'nullable|string',
            'type' => 'nullable|in:remotely,normal',

        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting = Meeting::create($data);
        $users = User::all();
        foreach ($users as $user) {
            $notificationData = [
                'user_id' => $user->id,
                'notifiable_id' => $meeting->id,
                'title' => 'دعوة',
                'message'  => ' تمت دعوتك لحضور الاجتماع ' . $meeting->name . ' بتاريخ ' . $meeting->datetime,
            ];
            $meeting->notifications()->create($notificationData);
        }

        // Notification::notifiable($users);
        return [
            'message' => 'تم إنشاء الاجتماع بنجاح',
            'data' => new MeetingResource($meeting),
        ];
    }


    public function updateMeeting(Meeting $meeting, array $data)
    {
        if (($meeting->user_id) != Auth::id()); {
            return response()->json([
                'message' => 'هذا الاجتماع ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'datetime' => 'nullable|date',
            'link' => 'nullable|string',
            'name' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'description' => 'nullable|string',
            'type' => 'nullable|in:remotely,normal',

        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting->update($data);
        return [
            'message' => 'تم تحديث الاجتماع بنجاح',
            'data' => new MeetingResource($meeting),
        ];
    }

    public function deleteMeeting(Meeting $meeting)
    {
        if (($meeting->user_id) != Auth::id()); {
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
        $meetings = Meeting::paginate($perPage, ['*'], 'page', $page);
        $meetingResource =  MeetingResource::collection($meetings);
        $paginationData = $this->paginationService->getPaginationData($meetings);

        return  [
            'data' => $meetingResource,
            'metadata' => $paginationData,
        ];;
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
