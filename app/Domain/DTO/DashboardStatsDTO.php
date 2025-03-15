<?php

namespace App\Domain\DTO;

class DashboardStatsDTO
{
    public function __construct(
        public int $userActive,
        public int $userNotActive,
        public int $accountCharitySaving,
        public int $accountInvestment,
        public int $job,
        public int $project,
        public int $course,
        public int $discount
    ) {}

    public function toArray(): array
    {
        return [
            'userActive' => $this->userActive,
            'userNotActive' => $this->userNotActive,
            'accountCharitySaving' => $this->accountCharitySaving,
            'accountInvestment' => $this->accountInvestment,
            'job' => $this->job,
            'project' => $this->project,
            'course' => $this->course,
            'discount' => $this->discount,
        ];
    }
}
