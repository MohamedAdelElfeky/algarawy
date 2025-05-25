<?php

namespace App\Domain\Services\V2;

use App\Domain\Models\PendingUser;
use App\Models\User;

class UserCheckService
{
    public function checkEmailExists(string $email): bool
    {
        return $this->checkVerifiedUser('email', $email);
    }

    public function checkPhoneExists(string $phone): bool
    {
        // $sanitizedPhone = $this->sanitizePhone($phone);
        return $this->checkVerifiedPhone($phone);
    }

    private function checkVerifiedPhone(string $phone): bool
    {
        return User::where(function ($query) use ($phone) {
            $query->where('phone', $phone)
                ->orWhere('phone', 'LIKE', "%{$phone}");
        })
            ->whereNull('deleted_at')
            ->exists();
    }

    private function checkVerifiedUser(string $field, string $value): bool
    {
        return User::where($field, $value)
            ->whereNull('deleted_at')
            ->exists();
    }
    
    public function checkNationalIdExists(string $nationalId): bool
    {
        return $this->checkVerifiedUser('national_id', $nationalId);
    }

    // private function sanitizePhone(string $phone): string
    // {
    //     $phone = preg_replace('/[^0-9]/', '', $phone);
    //     return ltrim($phone, '0');
    // }
}
