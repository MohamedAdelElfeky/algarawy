<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MeetingService;
use Illuminate\Http\Request;

class ApiMeetingController extends Controller
{
    protected $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }

    public function store(Request $request)
    {
        $meeting = $this->meetingService->createMeeting($request->all());

        return response()->json($meeting, 201);
    }

    public function update(Request $request, $id)
    {
        $meeting = $this->meetingService->getMeeting($id);

        $updatedMeeting = $this->meetingService->updateMeeting($meeting, $request->all());

        return response()->json($updatedMeeting);
    }

    public function destroy($id)
    {
        $meeting = $this->meetingService->getMeeting($id);

        $this->meetingService->deleteMeeting($meeting);

        return response()->json(['message' => 'تم حذف الاجتماع بنجاح'], 200);
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $meetings = $this->meetingService->getAllMeetings($perPage, $page);
        return response()->json($meetings, 200);
    }

    public function show($id)
    {
        $meeting = $this->meetingService->getMeeting($id);

        if (!$meeting) {
            return response()->json(['message' => 'الاجتماع غير موجود'], 404);
        }

        return response()->json($meeting);
    }
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $results = $this->meetingService->searchMeeting($searchTerm);
        return response()->json(['data' => $results]);
    }
}
