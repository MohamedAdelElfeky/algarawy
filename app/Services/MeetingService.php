<?php

namespace App\Services;

use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
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
            'datetime' => 'required|date',
            'link' => 'required|string',
            'name' => 'required|string',
            'from' => 'required|integer',
            'to' => 'required|integer',
            'description' => 'required|string',
            'type' => 'required|in:remotely,normal',

        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting = Meeting::create($data);

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
            'name' => 'nullable|string',
            'from' => 'nullable|date_format:H:i',
            'to' => 'nullable|date_format:H:i',
            'link' => 'nullable|string|url',
            'description' => 'nullable|string',
            'type' => 'required|in:remotely,normal',

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
