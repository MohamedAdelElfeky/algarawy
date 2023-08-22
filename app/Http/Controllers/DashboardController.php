<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\ProjectResource;
use App\Models\BankAccount;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Job;
use App\Models\Project;
use App\Models\User;
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
        // addVendors(['amcharts', 'amcharts-maps', 'amcharts-stock']);
        $userActive = User::where('registration_confirmed', 1)->where('admin', 0)->count();
        $userNotActive = User::where('registration_confirmed', 0)->where('admin', 0)->count();
        $accountCharitySaving = BankAccount::whereIn('type', ['charity', 'saving'])->count();
        $accountInvestment = BankAccount::where('type', 'investment')->count();
        $job = Job::count();
        $project = Project::count();
        $course = Course::count();
        $discount = Discount::count();
        return view(
            'pages.dashboards.index',
            compact(
                'userActive',
                'userNotActive',
                'accountCharitySaving',
                'accountInvestment',
                'job',
                'project',
                'course',
                'discount'
            )
        );
    }
}
