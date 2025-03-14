<?php

namespace App\Domain\Services;

use App\Domain\Models\BankAccount;

class BankAccountService
{
    public function getAllBankAccounts()
    {
        return BankAccount::all();
    }

    public function createBankAccount(array $data)
    {
        return BankAccount::create($data);
    }

    public function getBankAccountById(string $id)
    {
        return BankAccount::find($id);
    }

    public function updateBankAccount(string $id, array $data)
    {
        $bankAccount = BankAccount::find($id);

        if ($bankAccount) {
            $bankAccount->update($data);
        }

        return $bankAccount;
    }

    public function deleteBankAccount(string $id): bool
    {
        $bankAccount = BankAccount::find($id);

        if ($bankAccount) {
            return $bankAccount->delete();
        }

        return false;
    }

    public function getAccountsByType(array $types)
    {
        return BankAccount::whereIn('type', $types)->paginate(25);
    }

    public function changeStatus($id, $status)
    {
        $bankAccount = BankAccount::find($id);

        if ($bankAccount) {
            $bankAccount->update(['status' => $status]);
            return true;
        }

        return false;
    }

    public function getSavingBankAccounts()
    {
        return BankAccount::where('type', 'saving')->where('status', 'active')->get();
    }

    public function getCharityBankAccounts()
    {
        return BankAccount::where('type', 'charity')->where('status', 'active')->get();
    }

    public function getCharityAndSavingBankAccounts()
    {
        return [
            'charity_account' => BankAccount::where('type', 'charity')->where('status', 'active')->first(),
            'saving_account' => BankAccount::where('type', 'saving')->where('status', 'active')->first(),
            'investment_account' => BankAccount::where('type', 'investment')->where('status', 'active')->first(),
        ];
    }
}
