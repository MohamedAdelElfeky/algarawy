<?php

namespace App\Domain\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    // public function getAllUsers();
    public function findUserById($id);
    public function createUser(array $data);
    public function updateUser(User $user, array $data);
    public function deleteUser(User $user);
    public function findByNationalId(string $nationalId);
    public function countActiveUsers(): int;
    public function countInactiveUsers(): int;
}
