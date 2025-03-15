<?php
namespace App\Domain\Repositories;

use App\Domain\Models\BankAccount;

interface BankAccountRepository
{
    public function countCharityAndSavingAccounts(): int;
    public function countInvestmentAccounts(): int;
}
