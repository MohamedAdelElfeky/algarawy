<?php

namespace App\Services;

use App\Models\Meeting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class MeetingService
{

    public function createMeeting(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'datetime' => 'required|date',
            'link' => 'required|string',
            'description' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting = Meeting::create($data);

        return response()->json([
            'message' => 'Meeting created successfully',
            'data' => $meeting,
        ]);
    }


    public function updateMeeting(Meeting $meeting, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'datetime' => 'required|date',
            'link' => 'required|string',
            'description' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting->update($data);

        return response()->json([
            'message' => 'Meeting updated successfully',
            'data' => $meeting,
        ]);
    }

    public function deleteMeeting(Meeting $meeting)
    {
        // Delete the meeting
        $meeting->delete();
    }

    public function getMeeting(string $id)
    {
        return Meeting::findOrFail($id);
    }

    public function getAllMeetings()
    {
        // Retrieve all meetings
        return Meeting::all();
    }
}
