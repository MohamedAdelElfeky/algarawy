<?php

namespace App\Services;

use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MeetingService
{

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
            'message' => 'Meeting created successfully',
            'data' => new MeetingResource($meeting),
        ];
    }


    public function updateMeeting(Meeting $meeting, array $data)
    {
        $validator = Validator::make($data, [
            'datetime' => 'required|date',
            'name' => 'required|string',
            'from' => 'required|integer',
            'to' => 'required|integer',
            'link' => 'required|string|url',
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

        $meeting->update($data);
        return[
            'message' => 'Meeting updated successfully',
            'data' => new MeetingResource($meeting),
        ];
    }

    public function deleteMeeting(Meeting $meeting)
    {
        $meeting->delete();
    }

    public function getMeeting(string $id)
    {
        return Meeting::findOrFail($id);
    }

    public function getAllMeetings()
    {
        $meetings = Meeting::all();
        return MeetingResource::collection($meetings);
    }
}
