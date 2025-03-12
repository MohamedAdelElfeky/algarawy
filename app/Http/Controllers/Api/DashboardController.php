<?php

namespace App\Http\Controllers\Api;

use App\Applications\Services\DashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function __construct(private DashboardService $dashboardService)
    {
        $this->middleware('optional.auth')->only('getDataDashboard');
        $this->middleware('auth:sanctum')->except('getDataDashboard');
    }

    public function getAuthenticatedDataDashboard()
    {
        return $this->dashboardService->getDashboardData(true);
    }

    public function getDataDashboard()
    {
        return Auth::guard('sanctum')->check()
            ? $this->getAuthenticatedDataDashboard()
            : $this->dashboardService->getDashboardData(false);
    }
}
