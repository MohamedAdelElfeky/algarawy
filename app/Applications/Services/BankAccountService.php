<?php
namespace App\Applications\Services;

use App\Domain\BankAccount\Entities\BankAccount;
use App\Domain\BankAccount\Repositories\BankAccountRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class BankAccountService
{
    private BankAccountRepositoryInterface $repository;

    public function __construct(BankAccountRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllBankAccounts()
    {
        return $this->repository->getAll();
    }

    public function createBankAccount(array $data)
    {
        $validator = Validator::make($data, [
            'account_number' => 'required',
            'iban' => 'required',
            'bank_name' => 'required',
            'swift_number' => 'required',
            'type' => 'required|in:saving,charity,investment',
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $bankAccount = new BankAccount(
            $data['account_number'],
            $data['iban'],
            $data['bank_name'],
            $data['swift_number'],
            $data['type'],
            auth()->id()
        );

        return $this->repository->create($bankAccount);
    }

    public function getBankAccountById(string $id)
    {
        return $this->repository->getById($id) ?? ['message' => 'Bank account not found'];
    }

    public function updateBankAccount(string $id, array $data)
    {
        $validator = Validator::make($data, [
            'account_number' => 'required',
            'iban' => 'required',
            'bank_name' => 'required',
            'swift_number' => 'required',
            'type' => 'required|in:saving,charity,investment',
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        return $this->repository->update($id, $data) ?? ['message' => 'Bank account not found'];
    }

    public function deleteBankAccount(string $id)
    {
        return $this->repository->delete($id) ? ['message' => 'Bank account deleted successfully'] : ['message' => 'Bank account not found'];
    }

    public function getBankAccountsByType(string $type)
    {
        return $this->repository->getByType($type);
    }
}
