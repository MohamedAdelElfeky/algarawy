<?php

namespace App\Infrastructure;

use App\Domain\BankAccount\Entities\BankAccount;
use App\Domain\BankAccount\Repositories\BankAccountRepositoryInterface;
use App\Models\BankAccount as BankAccountModel;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentBankAccountRepository implements BankAccountRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return BankAccountModel::paginate(5);
    }

    public function getById(string $id): ?BankAccount
    {
        $bankAccount = BankAccountModel::find($id);
        return $bankAccount ? $this->mapToDomain($bankAccount) : null;
    }

    public function create(BankAccount $bankAccount): BankAccount
    {
        $bankAccountModel = BankAccountModel::create([
            'id' => $bankAccount->getId(),
            'account_number' => $bankAccount->getAccountNumber(),
            'iban' => $bankAccount->getIban(),
            'bank_name' => $bankAccount->getBankName(),
            'swift_number' => $bankAccount->getSwiftNumber(),
            'type' => $bankAccount->getType(),
            'user_id' => $bankAccount->getUserId(),
        ]);

        return $this->mapToDomain($bankAccountModel);
    }

    public function update(string $id, array $data): ?BankAccount
    {
        $bankAccountModel = BankAccountModel::find($id);
        if (!$bankAccountModel) return null;

        $bankAccountModel->update($data);
        return $this->mapToDomain($bankAccountModel);
    }

    public function delete(string $id): bool
    {
        return BankAccountModel::destroy($id) > 0;
    }

    public function getByType(string $type): LengthAwarePaginator
    {
        return BankAccountModel::where('type', $type)->paginate(5);
    }

    private function mapToDomain(BankAccountModel $bankAccountModel): BankAccount
    {
        return new BankAccount(
            $bankAccountModel->account_number,
            $bankAccountModel->iban,
            $bankAccountModel->bank_name,
            $bankAccountModel->swift_number,
            $bankAccountModel->type,
            $bankAccountModel->user_id,
            $bankAccountModel->id
        );
    }
}
