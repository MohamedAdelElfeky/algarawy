<?php

namespace App\Domain\Repositories;

use App\Models\User;
use App\Domain\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;

class UserWebRepository
{
    public function getUsersByRole(string $role, int $perPage = 25): LengthAwarePaginator
    {
        return User::whereHas('roles', fn($query) => $query->where('name', $role))
            ->with(['details', 'roles'])
            ->paginate($perPage);
    }

    public function getUsersBySetting(string $settingKey, int $value, int $perPage = 25): LengthAwarePaginator
    {
        $settingId = Setting::where('key', $settingKey)->value('id');

        return User::whereHas('roles', fn($query) => $query->where('name', 'user'))
            ->whereHas('userSettings', fn($query) => $query->where('setting_id', $settingId)->where('value', $value))
            ->with(['details', 'roles', 'userSettings.setting'])
            ->paginate($perPage);
    }

    public function findUserById(int $id): ?User
    {
        return User::find($id);
    }
    
}
