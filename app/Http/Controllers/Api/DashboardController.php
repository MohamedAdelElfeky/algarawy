<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\Course;
use App\Domain\Models\Discount;
use App\Domain\Models\Job;
use App\Domain\Models\Meeting;
use App\Domain\Models\Project;
use App\Domain\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\DiscountResource;
use App\Http\Resources\JobResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('optional.auth')->only('getDataDashboard');
        $this->middleware('auth:sanctum')->except('getDataDashboard');
    }

    public function getAuthenticatedDataDashboard()
    {
        return $this->getData();
    }

    public function getDataDashboard()
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return redirect()->route('dashboard.authenticated');
        } else {
            return $this->getPublicData();
        }
    }

    /**
     * Get public data for non-authenticated users.
     *
     * @return array
     */
    private function getPublicData()
    {
        // Fetch data with public status
        $jobs = JobResource::collection(Job::visibilityStatus('public')->ApprovalStatus('approved')->paginate(5));
        $courses = CourseResource::collection(Course::visibilityStatus('public')->ApprovalStatus('approved')->paginate(5));
        $projects = ProjectResource::collection(Project::visibilityStatus('public')->ApprovalStatus('approved')->paginate(5));
        $meetings = MeetingResource::collection(Meeting::visibilityStatus('public')->ApprovalStatus('approved')->paginate(5));
        $discounts = DiscountResource::collection(Discount::visibilityStatus('public')->ApprovalStatus('approved')->paginate(5));
        $services = ServiceResource::collection(Service::visibilityStatus('public')->ApprovalStatus('approved')->paginate(5));

        $oneRowArray = [
            'Job' => $jobs,
            'Course' => $courses,
            'Project' => $projects,
            'Meeting' => $meetings,
            'Discount' => $discounts,
            'Service' => $services,
        ];

        $result = [
            "date" => [],
        ];

        foreach ($oneRowArray as $type => $data) {
            $formattedData = [
                "type" => $type,
                "data" => $data,
            ];

            $result["date"][] = $formattedData;
        }

        return $result;
    }

    private function getData()
    {
        $user = Auth::guard('sanctum')->user();

        $showNoComplaintedPosts = $user->userSettings()
            ->whereHas('setting', function ($query) {
                $query->where('key', 'show_no_complaints_posts');
            })
            ->value('value') ?? false;
        $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id')->toArray();

        // Prepare queries for each model
        $jobsQuery = Job::whereNotIn('user_id', $blockedUserIds);
        $coursesQuery = Course::ApprovalStatus('approved')->whereNotIn('user_id', $blockedUserIds);
        $projectsQuery = Project::ApprovalStatus('approved')->whereNotIn('user_id', $blockedUserIds);
        $meetingsQuery = Meeting::ApprovalStatus('approved')->whereNotIn('user_id', $blockedUserIds);
        $discountsQuery = Discount::ApprovalStatus('approved')->whereNotIn('user_id', $blockedUserIds);
        $servicesQuery = Service::ApprovalStatus('approved')->whereNotIn('user_id', $blockedUserIds);

        // Apply complaint filter to each query
        $this->applyComplaintFilter($jobsQuery, $showNoComplaintedPosts, $user);
        $this->applyComplaintFilter($coursesQuery, $showNoComplaintedPosts, $user);
        $this->applyComplaintFilter($projectsQuery, $showNoComplaintedPosts, $user);
        $this->applyComplaintFilter($meetingsQuery, $showNoComplaintedPosts, $user);
        $this->applyComplaintFilter($discountsQuery, $showNoComplaintedPosts, $user);
        $this->applyComplaintFilter($servicesQuery, $showNoComplaintedPosts, $user);

        // Fetch paginated data
        $jobs = JobResource::collection($jobsQuery->paginate(5));
        $courses = CourseResource::collection($coursesQuery->paginate(5));
        $projects = ProjectResource::collection($projectsQuery->paginate(5));
        $meetings = MeetingResource::collection($meetingsQuery->paginate(5));
        $discounts = DiscountResource::collection($discountsQuery->paginate(5));
        $services = ServiceResource::collection($servicesQuery->paginate(5));

        $oneRowArray = [
            'Job' => $jobs,
            'Course' => $courses,
            'Project' => $projects,
            'Meeting' => $meetings,
            'Discount' => $discounts,
            'Service' => $services,
        ];

        $result = [
            "date" => [],
        ];

        foreach ($oneRowArray as $type => $data) {
            $formattedData = [
                "type" => $type,
                "data" => $data,
            ];

            $result["date"][] = $formattedData;
        }

        return $result;
    }

    private function applyComplaintFilter($query, $showNoComplaintedPosts, $user)
    {
        if ($showNoComplaintedPosts) {
            $query->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereDoesntHave('complaints');
            });
        }
        return $query;
    }
}
