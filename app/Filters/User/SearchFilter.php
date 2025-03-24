<?php

namespace App\Filters\User;

use Illuminate\Database\Eloquent\Builder;

class SearchFilter extends Filter
{
    public function __construct(private ?string $searchTerm) {}

    protected function shouldApply(): bool
    {
        return !empty($this->searchTerm);
    }

    protected function applyFilter(Builder $query): void
    {
        $query->where(function ($query) {
            $fields = ['first_name', 'last_name', 'phone'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $this->searchTerm . '%');
            }
        });
    }
}
