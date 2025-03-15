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

    private function getAuthenticatedData(): array
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return [];
        }

        return $this->dashboardRepository->getData([
            'approval' => 'approved',
            'showNoComplaintedPosts' => $this->shouldShowNoComplaintsPosts($user),
            'user_id' => $user->id,
        ], $this->getBlockedUserIds($user));
    }

    private function getBlockedUserIds($user): array
    {
        return $user->blockedUsers()->pluck('blocked_user_id')->toArray();
    }

    private function shouldShowNoComplaintsPosts($user): bool
    {
        return (bool) $user->userSettings()
            ->whereHas('setting', fn($query) => $query->where('key', 'show_no_complaints_posts'))
            ->value('value');
    }
}
