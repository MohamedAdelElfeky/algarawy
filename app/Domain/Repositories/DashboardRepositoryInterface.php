<?php

namespace App\Domain\Repositories;

interface DashboardRepositoryInterface
{
    public function getData(array $filters = [], array $blockedUserIds = []): array;
}
