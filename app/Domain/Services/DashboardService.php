<?php

namespace App\Domain\Services;

use App\Domain\DTO\DashboardStatsDTO;
use App\Infrastructure\EloquentBankAccountRepository;
use App\Infrastructure\EloquentCourseRepository;
use App\Infrastructure\EloquentDiscountRepository;
use App\Infrastructure\EloquentJobRepository;
use App\Infrastructure\EloquentProjectRepository;
use App\Infrastructure\EloquentUserRepository;

class DashboardService
{
    public function __construct(
        private EloquentUserRepository $userRepository,
        private EloquentBankAccountRepository $bankAccountRepository,
        private EloquentJobRepository $jobRepository,
        private EloquentProjectRepository $projectRepository,
        private EloquentCourseRepository $courseRepository,
        private EloquentDiscountRepository $discountRepository
    ) {}

    public function getDashboardStats(): DashboardStatsDTO
    {
        return new DashboardStatsDTO(
            userActive: $this->userRepository->countActiveUsers(),
            userNotActive: $this->userRepository->countInactiveUsers(),
            accountCharitySaving: $this->bankAccountRepository->countCharityAndSavingAccounts(),
            accountInvestment: $this->bankAccountRepository->countInvestmentAccounts(),
            job: $this->jobRepository->count(),
            project: $this->projectRepository->count(),
            course: $this->courseRepository->count(),
            discount: $this->discountRepository->count()
        );
    }
}
