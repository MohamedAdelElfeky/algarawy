<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MeetingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiMeetingController extends Controller
{
    protected $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }

    public function store(Request $request): JsonResponse
    {
        $meeting = $this->meetingService->createMeeting($request->all());

        return response()->json($meeting, 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $meeting = $this->meetingService->getMeeting($id);

        $updatedMeeting = $this->meetingService->updateMeeting($meeting, $request->all());

        return response()->json($updatedMeeting);
    }

    public function destroy($id): JsonResponse
    {
        $meeting = $this->meetingService->getMeeting($id);

        $this->meetingService->deleteMeeting($meeting);

        return response()->json(['message' => 'Meeting deleted successfully'], 200);
    }

    public function index(): JsonResponse
    {
        $meetings = $this->meetingService->getAllMeetings();

        return response()->json($meetings);
    }

    public function show($id): JsonResponse
    {
        $meeting = $this->meetingService->getMeeting($id);

        if (!$meeting) {
            return response()->json(['message' => 'Meeting not found'], 404);
        }

        return response()->json($meeting);
    }
}