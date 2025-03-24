<?php

namespace App\Filters\User;

use Illuminate\Database\Eloquent\Builder;

class CityFilter extends Filter
{
    public function __construct(private ?int $cityId) {}

    protected function shouldApply(): bool
    {
        return !empty($this->cityId);
    }

    protected function applyFilter(Builder $query): void
    {
        $query->whereHas('details', function (Builder $q) {
            $q->where('city_id', $this->cityId);
        });
    }
}
