<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\BankAccountRepositoryInterface;
use App\Infrastructure\Repositories\BankAccountRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(BankAccountRepositoryInterface::class, BankAccountRepository::class);
    }
}
