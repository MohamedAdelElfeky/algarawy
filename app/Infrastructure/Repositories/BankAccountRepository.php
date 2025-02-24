<?php
namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\BankAccountRepositoryInterface;

class BankAccountRepository implements BankAccountRepositoryInterface
{
    public function getAll()
    {
        return BankAccount::all();
    }

    public function findById(string $id)
    {
        return BankAccount::find($id);
    }

    public function create(array $data)
    {
        return BankAccount::create($data);
    }

    public function update(string $id, array $data)
    {
        $bankAccount = $this->findById($id);
        if ($bankAccount) {
            $bankAccount->update($data);
            return $bankAccount;
        }
        return null;
    }

    public function delete(string $id)
    {
        return BankAccount::destroy($id);
    }
}
