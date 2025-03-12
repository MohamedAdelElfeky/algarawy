<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\DashboardRepositoryInterface;
use App\Domain\Models\{Course, Discount, Job, Meeting, Project, Service};
use App\Http\Resources\{CourseResource, DiscountResource, JobResource, MeetingResource, ProjectResource, ServiceResource};

class DashboardRepository implements DashboardRepositoryInterface
{
    protected array $models;
    protected array $resources;

    public function __construct()
    {
        $this->models = [
            'Job' => Job::query(),
            'Course' => Course::query(),
            'Project' => Project::query(),
            'Meeting' => Meeting::query(),
            'Discount' => Discount::query(),
            'Service' => Service::query(),
        ];

        $this->resources = [
            'Job' => JobResource::class,
            'Course' => CourseResource::class,
            'Project' => ProjectResource::class,
            'Meeting' => MeetingResource::class,
            'Discount' => DiscountResource::class,
            'Service' => ServiceResource::class,
        ];
    }

    public function getData(array $filters = [], array $blockedUserIds = []): array
    {
        $result = ['data' => []];

        foreach ($this->models as $type => $query) {
            $this->applyFilters($query, $filters, $blockedUserIds);

            $paginatedData = $query->paginate(5);
            $resourceClass = $this->resources[$type];

            $result['data'][] = [
                'type' => $type,
                'data' => $resourceClass::collection($paginatedData),
            ];
        }

        return $result;
    }

    private function applyFilters($query, array $filters, array $blockedUserIds): void
    {
        if (isset($filters['visibility'])) {
            $query->visibilityStatus($filters['visibility']);
        }
        if (isset($filters['approval'])) {
            $query->ApprovalStatus($filters['approval']);
        }
        if (!empty($blockedUserIds)) {
            $query->whereNotIn('user_id', $blockedUserIds);
        }
        if ($filters['showNoComplaintedPosts'] ?? false) {
            $query->where(function ($q) use ($filters) {
                $q->where('user_id', $filters['user_id'])->orWhereDoesntHave('complaints');
            });
        }
    }
}
