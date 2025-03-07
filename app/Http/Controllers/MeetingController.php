<?php

namespace App\Http\Controllers;

use App\Domain\Models\Meeting;
use App\Http\Resources\MeetingResource;
use App\Services\MeetingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingController extends Controller
{

    protected $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $response = $this->meetingService->createMeeting($data);

        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $meeting = $this->meetingService->getMeeting($id);

        return response()->json([
            'data' => new MeetingResource($meeting),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        $meeting = Meeting::findOrFail($id);

        $response = $this->meetingService->updateMeeting($meeting, $data);

        return $response;
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

    public function changeStatus(Request $request, Meeting $meeting)
    {
        $request->validate([
            'status' => 'in:public,private',
        ]);

        $meeting->update(['status' => $request->status]);

        return back()->with('status', 'Meeting status updated successfully!');
    }
}
