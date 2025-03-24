<?php

namespace App\Filters\User;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    public function handle(Builder $query, \Closure $next)
    {
        if ($this->shouldApply()) {
            $this->applyFilter($query);
        }
        return $next($query);
    }

    abstract protected function shouldApply(): bool;

    abstract protected function applyFilter(Builder $query): void;
}
