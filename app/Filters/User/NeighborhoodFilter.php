<?php

namespace App\Filters\User;

use Illuminate\Database\Eloquent\Builder;


class NeighborhoodFilter extends Filter
{
    public function __construct(private ?int $neighborhoodId) {}

    protected function shouldApply(): bool
    {
        return !empty($this->neighborhoodId);
    }

    protected function applyFilter(Builder $query): void
    {
        $query->whereHas('details', function (Builder $q) {
            $q->where('neighborhood_id', $this->neighborhoodId);
        });
        }
}
