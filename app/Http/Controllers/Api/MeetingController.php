<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\MeetingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRequest;
use App\Http\Resources\MeetingResource;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function __construct(private MeetingService $MeetingService)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $Meetings = $this->MeetingService->getMeetings($perPage, $page);
        return response()->json($Meetings, 200);
    }

    public function show($id)
    {
        return new MeetingResource($this->MeetingService->getMeetingById($id));
    }

    public function store(MeetingRequest $request)
    {
        $Meeting = $this->MeetingService->createMeeting($request);
        return response()->json($Meeting, 201);
    }

    public function update(MeetingRequest $request, $id)
    {
        $Meeting = $this->MeetingService->getMeetingById($id);
        $updatedMeeting = $this->MeetingService->updateMeeting($Meeting, $request);
        return response()->json($updatedMeeting);
    }

    public function destroy($id)
    {
        return $this->MeetingService->deleteMeeting($id);
    }

    public function search(Request $request)
    {
        return $this->MeetingService->searchMeeting($request->get('search'));
    }
}

