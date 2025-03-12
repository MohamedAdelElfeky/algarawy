<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\Meeting;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRequest;
use App\Services\MeetingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{

    public function __construct(private MeetingService $meetingService)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
    }
    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $meetings =  $this->meetingService->getMeetings($perPage, $page);
        return response()->json($meetings, 200);
    }

    public function store(MeetingRequest $request)
    {
        $meeting = $this->meetingService->createMeeting($request);
        return response()->json($meeting, 201);
    }

    public function update(MeetingRequest $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        if (!$meeting) {
            return response()->json(['message' => 'الاجتماع غير موجودة'], 404);
        }
        $updatedMeeting = $this->meetingService->updateMeeting($meeting, $request);

        return response()->json($updatedMeeting);
    }

    public function destroy($id)
    {
        $meeting = $this->meetingService->getMeeting($id);

        $this->meetingService->deleteMeeting($meeting);

        return response()->json(['message' => 'تم حذف الاجتماع بنجاح'], 200);
    }

  
    public function getAuthenticatedMeetings(Request $request)
    {
        $perPage = $request->query('perPage', 10);
        $page = $request->query('page', 1);

        $user = Auth::user();

        if ($user) {
            $meetings = $this->meetingService->getAllMeetings($perPage, $page);
            return response()->json($meetings, 200);
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }

    // public function index(Request $request)
    // {
    //     $perPage = $request->header('per_page');
    //     $page = $request->header('page');
    //     $meetings = $this->meetingService->getAllMeetings($perPage, $page);
    //     return response()->json($meetings, 200);
    // }

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

    public function getMeetings(Request $request)
    {
        $perPage = $request->header('per_page');
        $page = $request->header('page');
        $meetings = $this->meetingService->getAllMeetingsPublic($perPage, $page);
        return response()->json($meetings, 200);
    }
}
