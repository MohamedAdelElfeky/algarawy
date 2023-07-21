<?php

namespace App\Services;

use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MeetingService
{

    public function createMeeting(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'datetime' => 'required|date',
            'link' => 'required|string',
            'name' => 'required|string',
            'from' => 'required|integer',
            'to' => 'required|integer',
            'description' => 'required|string',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting = Meeting::create($data);

        return response()->json([
            'message' => 'Meeting created successfully',
            'data' => new MeetingResource($meeting),
        ]);
    }


    public function updateMeeting(Meeting $meeting, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'datetime' => 'required|date',
            'name' => 'required|string',
            'from' => 'required|integer',
            'to' => 'required|integer',
            'link' => 'required|string',
            'description' => 'required|string',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting->update($data);
        return response()->json([
            'message' => 'Meeting updated successfully',
            'data' => new MeetingResource($meeting),
        ]);
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
