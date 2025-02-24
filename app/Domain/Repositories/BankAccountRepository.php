<?php
namespace App\Domain\Repositories;

use App\Domain\Models\BankAccount;

interface BankAccountRepository
{
    public function findAll(): array;
    public function findById(string $id): ?BankAccount;
    public function save(BankAccount $bankAccount): void;
    public function delete(string $id): void;
    public function findByType(string $type): array;
    public function findByStatus(string $status): array;
}
