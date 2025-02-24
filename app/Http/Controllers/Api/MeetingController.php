<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\Meeting;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRequest;
use Illuminate\Support\Facades\Auth;
use App\Domain\Services\MeetingService;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    protected $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        // $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum');
        $this->meetingService = $meetingService;
    }

    public function store(MeetingRequest $request)
    {
        $meeting = $this->meetingService->createMeeting($request->validated());
        return response()->json($meeting, 201);
    }

    public function update(MeetingRequest $request, Meeting $meeting)
    {
        $updatedMeeting = $this->meetingService->updateMeeting($meeting, $request->validated());
        return response()->json($updatedMeeting);
    }

    public function destroy(Meeting $meeting)
    {
        $this->meetingService->deleteMeeting($meeting);
        return response()->json(['message' => 'تم حذف الاجتماع بنجاح'], 200);
    }

    public function show($id)
    {
        $meeting = $this->meetingService->getMeeting($id);
        return response()->json($meeting);
    }

    public function index(Request $request)
    {
        $user = $request->auth_user;

        // if ($user) {
        //     return response()->json([
        //         'message' => 'تم تسجيل الدخول',
        //         'user' => $user
        //     ], 200);
        // }

        // return response()->json([
        //     'message' => 'أنت غير مسجل دخول'
        // ], 200);
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);
        $meetings = $this->meetingService->getAllMeetings($perPage, $page);
        return response()->json($meetings, 200);
    }
}
