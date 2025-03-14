<?php

namespace App\Http\Controllers;

use App\Domain\Services\MeetingService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MeetingController extends Controller
{

    public function __construct(private MeetingService $meetingService) {}

    public function index(): View
    {
        $meetings = $this->meetingService->getPaginatedMeeting(25);
        return view('pages.dashboards.meeting.index', compact('meetings'));
    }


    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->meetingService->deleteMeeting($id, 'web');
        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'deleted successfully.' : 'Failed to delete.'
        ]);
    }
}
