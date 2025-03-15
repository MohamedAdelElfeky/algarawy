<?php

namespace App\Infrastructure;

use App\Domain\Models\BankAccount;
use App\Domain\Repositories\BankAccountRepository;

class EloquentBankAccountRepository implements BankAccountRepository
{
    public function countCharityAndSavingAccounts(): int
    {
        return BankAccount::whereIn('type', ['charity', 'saving'])->count();
    }

    public function countInvestmentAccounts(): int
    {
        return BankAccount::where('type', 'investment')->count();
    }
}
