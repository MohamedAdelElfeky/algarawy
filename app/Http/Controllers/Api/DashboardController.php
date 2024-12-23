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
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{



    public function getDataDashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Get the user's preference for showing posts without complaints
        $showNoComplaintedPosts = $user->show_no_complainted_posts == 1;
        $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id')->toArray();

        // Prepare queries for each model
        $jobsQuery = Job::whereNotIn('user_id', $blockedUserIds);
        $coursesQuery = Course::whereNotIn('user_id', $blockedUserIds);
        $projectsQuery = Project::whereNotIn('user_id', $blockedUserIds);
        $meetingsQuery = Meeting::whereNotIn('user_id', $blockedUserIds);
        $discountsQuery = Discount::whereNotIn('user_id', $blockedUserIds);
        $servicesQuery = Service::whereNotIn('user_id', $blockedUserIds);

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

        // $jobs = JobResource::collection(Job::paginate(5));
        // $courses = CourseResource::collection(Course::paginate(5));
        // $projects = ProjectResource::collection(Project::paginate(5));
        // $meetings =   MeetingResource::collection(Meeting::paginate(5));
        // $discounts = DiscountResource::collection(Discount::paginate(5));
        // $services = ServiceResource::collection(Service::paginate(5));
        $oneRowArray = [
            'Course' => $courses,
            'Project' => $projects,
            'Meeting' => $meetings,
            'Discount' => $discounts,
            'Service' => $services,
            // 'الوظيفة' => $jobs,
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
        // $jsonResult = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $result;
    }

    /**
     * Apply the complaint filter to the query based on user preferences.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $showNoComplaintedPosts
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyComplaintFilter($query, $showNoComplaintedPosts, $user)
    {
        if ($showNoComplaintedPosts) {
            $query->whereDoesntHave('complaints', function ($query) use ($user) {
                $query->where('user_id', '<>', $user->id); // Exclude user complaints
            });
        } else {
            $query->has('complaints');
        }

        return $query;
    }
}
