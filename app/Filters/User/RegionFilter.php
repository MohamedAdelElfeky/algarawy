<?php

namespace App\Filters\User;

use Illuminate\Database\Eloquent\Builder;

class RegionFilter extends Filter
{
    public function __construct(private ?int $regionId) {}

    protected function shouldApply(): bool
    {
        return !empty($this->regionId);
    }

    protected function applyFilter(Builder $query): void
    {
        $query->whereHas('details', function (Builder $q) {
            $q->where('region_id', $this->regionId);
        });
    }
}
