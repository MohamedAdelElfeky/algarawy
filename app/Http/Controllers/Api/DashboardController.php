<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\DiscountResource;
use App\Http\Resources\JobResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Job;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Service;

class DashboardController extends Controller
{



    public function getDataDashboard()
    {
        $jobs = JobResource::collection(Job::paginate(5));
        $courses = CourseResource::collection(Course::paginate(5));
        $projects = ProjectResource::collection(Project::paginate(5));
        $meetings =   MeetingResource::collection(Meeting::paginate(5));
        $discounts = DiscountResource::collection(Discount::paginate(5));
        $services = ServiceResource::collection(Service::paginate(5));
        $oneRowArray = [
            'الوظيفة' => $jobs,
            'الدورات والاستشارات' => $courses,
            'دعم المشاريع' => $projects,
            'الاجتماعات عائلية' => $meetings,
            'الخصومات والعروض' => $discounts,
            'خدمات' => $services,
        ];
        $result = [
            "date" => [],
        ];
        foreach ($oneRowArray as $name => $data) {
            $formattedData = [
                "name" => $name,
                "data" => $data,
            ];

            $result["date"][] = $formattedData;
        }
        $jsonResult = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $jsonResult;
    }
}
