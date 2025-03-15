<?php

namespace App\Infrastructure;

use App\Domain\Models\Setting;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Http\Resources\UserResource;
use App\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findUserById($id)
    {
        $user = User::find($id);
        return $user ? new UserResource($user) : null;
    }

    public function getAllUsers()
    {
        $users = User::all();
        return UserResource::collection($users);
    }

    public function createUser(array $data)
    {
        $user = User::create($data);
        return new UserResource($user);
    }

    public function updateUser(User $user, array $data)
    {
        $user->update($data);
        return new UserResource($user);
    }

    public function deleteUser(User $user)
    {
        return $user->delete();
    }

    public function createUserDetail(User $user, array $data)
    {
        return $user->details()->create($data);
    }

    public function findByNationalId(string $nationalId)
    {
        $user = User::with(['userSettings', 'details.images'])->where('national_id', $nationalId)->first();
        return $user ? new UserResource($user) : null;
    }

    public function countActiveUsers(): int
    {
        $registrationConfirmedSetting = Setting::where('key', 'registration_confirmed')->first();
        return User::whereHas('userSettings', function ($query) use ($registrationConfirmedSetting) {
            $query->where('setting_id', $registrationConfirmedSetting->id)->where('value', 1);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->count();
    }

    public function countInactiveUsers(): int
    {
        $registrationConfirmedSetting = Setting::where('key', 'registration_confirmed')->first();
        return User::whereHas('userSettings', function ($query) use ($registrationConfirmedSetting) {
            $query->where('setting_id', $registrationConfirmedSetting->id)->where('value', 0);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->count();
    }
}
