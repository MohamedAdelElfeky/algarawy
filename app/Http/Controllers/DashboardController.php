<?php

namespace App\Http\Controllers;

use App\Domain\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index()
    {
        $stats = $this->dashboardService->getDashboardStats();
        return view('pages.dashboards.index', $stats->toArray());
    }
}

