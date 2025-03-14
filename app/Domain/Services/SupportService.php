<?php

namespace App\Domain\Services;

use App\Domain\Models\support;

class SupportService
{
    public function getSupportDetails(): ?Support
    {
        return support::first();
    }

    public function updateSupportDetails(array $data): Support
    {
        return Support::updateOrCreate([], $data);
    }
}
