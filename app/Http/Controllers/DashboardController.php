<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
use App\Services\MeetingService;

class DashboardController extends Controller
{
    protected $meetingService;
    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }
    public function index()
    {
        addVendors(['amcharts', 'amcharts-maps', 'amcharts-stock']);

        return view('pages.dashboards.index');
    }

    public function meeting()
    {
        $meetings = $this->meetingService->getAllMeetings();
        return response()->json(['data' => $meetings]);

    }
}
