<?php

namespace App\Http\Controllers;

use App\Domain\Models\BankAccount;
use App\Domain\Models\Course;
use App\Domain\Models\Discount;
use App\Domain\Models\Job;
use App\Domain\Models\Project;
use App\Domain\Models\Setting;
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
        $registrationConfirmedSetting = Setting::where('key', 'registration_confirmed')->first();
        $userActive = User::whereHas('userSettings', function ($query) use ($registrationConfirmedSetting) {
            $query->where('setting_id', $registrationConfirmedSetting->id)->where('value', 1);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->count();
    
        $userNotActive = User::whereHas('userSettings', function ($query) use ($registrationConfirmedSetting) {
            $query->where('setting_id', $registrationConfirmedSetting->id)->where('value', 0);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->count();
        // $userActive = User::where('registration_confirmed', 1)->where('admin', 0)->count();
        // $userNotActive = User::where('registration_confirmed', 0)->where('admin', 0)->count();
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
