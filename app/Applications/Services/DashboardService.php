<?php

namespace App\Applications\Services;

use App\Domain\Repositories\DashboardRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    protected DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardData(bool $isAuthenticated = false)
    {
        if ($isAuthenticated) {
            return $this->getAuthenticatedData();
        }
        return $this->getPublicData();
    }

    private function getPublicData()
    {
        return $this->dashboardRepository->getData([
            'visibility' => 'public',
            'approval' => 'approved'
        ]);
    }

    private function getAuthenticatedData()
    {
        $user = Auth::guard('sanctum')->user();
        $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id')->toArray();
        $showNoComplaintedPosts = $user->userSettings()
            ->whereHas('setting', fn($query) => $query->where('key', 'show_no_complaints_posts'))
            ->value('value') ?? false;

        return $this->dashboardRepository->getData([
            'approval' => 'approved',
            'showNoComplaintedPosts' => $showNoComplaintedPosts,
            'user_id' => $user->id,
        ], $blockedUserIds);
    }
}
