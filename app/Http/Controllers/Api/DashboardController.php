<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use App\Services\DiscountService;
use App\Services\JobService;
use App\Services\MeetingService;
use App\Services\ProjectService;
use App\Services\ServiceService;

class DashboardController extends Controller
{
    protected $jobService;
    protected $courseService;
    protected $projectService;
    protected $meetingService;
    protected $discountService;
    protected $serviceService;

    public function __construct(
        JobService $jobService,
        CourseService $courseService,
        ProjectService $projectService,
        MeetingService $meetingService,
        DiscountService $discountService,
        ServiceService $serviceService
    ) {
        $this->jobService = $jobService;
        $this->courseService = $courseService;
        $this->projectService = $projectService;
        $this->meetingService = $meetingService;
        $this->discountService = $discountService;
        $this->serviceService = $serviceService;
    }


    public function getDataDashboard()
    {
        $jobs = $this->jobService->getAllJobs(5, 1);
        $courses = $this->courseService->getAllCourses(5, 1);
        $projects = $this->projectService->getAllProjects(5, 1);
        $meetings = $this->meetingService->getAllMeetings(5, 1);
        $discounts = $this->discountService->getAllDiscounts(5, 1);
        $services = $this->serviceService->getAllServices(5, 1);

        $oneRowArray = [
            'الوظيفة' => $jobs,
            'الدورات والاستشارات' => $courses,
            'دعم المشاريع' => $projects,
            'الاجتماعات عائلية' => $meetings,
            'الخصومات والعروض' => $discounts,
            'خدمات' => $services,
        ];

        return response()->json($oneRowArray);
    }
}
