<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\ProjectResource;
use App\Models\Job;
use App\Models\Project;
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
        $jobs = Job::all();
        $allProject = Project::all();
        $jobData = JobResource::collection($jobs);
        $projects = ProjectResource::collection($allProject);
        return view('pages.dashboards.index', \compact('jobData', 'projects'));
    }

    public function meeting()
    {
        $meetings = $this->meetingService->getAllMeetings();
        return response()->json(['data' => $meetings]);
    }
}
