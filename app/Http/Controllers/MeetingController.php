<?php

namespace App\Http\Controllers;

use App\Domain\Models\Meeting;
use App\Http\Resources\MeetingResource;
use App\Services\MeetingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingController extends Controller
{

    public function __construct(private MeetingService $meetingService)
    {
    }
    public function index()
    {
        $meetings = Meeting::with([
            'user',
            'images',
            'pdfs',
            'likes',
            'favorites',
        ])->orderBy('created_at', 'desc')->paginate(25);
        return view('pages.dashboards.meeting.index', compact('meetings'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $meeting = $this->meetingService->getMeeting($id);

        $this->meetingService->deleteMeeting($meeting);

        return response()->json([
            'message' => 'Meeting deleted successfully',
        ]);
    }
}
