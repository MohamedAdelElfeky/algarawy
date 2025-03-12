<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\BankAccount;
use App\Http\Controllers\Controller;
use App\Http\Resources\BankAccountResource;
use App\Services\BankAccountService;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{

    public function __construct(private BankAccountService $bankAccountService)
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $bankAccounts = $this->bankAccountService->getAllBankAccounts();

        return response()->json($bankAccounts, 200);
    }

    public function store(Request $request)
    {
        $bankAccount = $this->bankAccountService->createBankAccount($request->all());

        return response()->json($bankAccount, 201);
    }

    public function show($id)
    {
        $bankAccount = $this->bankAccountService->getBankAccountById($id);

        return response()->json($bankAccount);
    }

    public function update(Request $request, $id)
    {
        $bankAccount = $this->bankAccountService->updateBankAccount($id, $request->all());

        return response()->json($bankAccount, 200);
    }

    public function destroy($id)
    {
        $this->bankAccountService->deleteBankAccount($id);

        return response()->json(['message' => 'Bank account deleted successfully']);
    }

    public function getSavings()
    {
        $bankAccounts = $this->bankAccountService->getSavingBankAccounts();

        return response()->json($bankAccounts, 200);
    }

    public function getCharities()
    {
        $bankAccounts = $this->bankAccountService->getCharityBankAccounts();

        return response()->json($bankAccounts, 200);
    }
    public function getCharityAndSavingBankAccounts()
    {
        $bankAccountCharity = BankAccount::where('type', 'charity')->where('status', 'active')->first();
        $bankAccountSaving = BankAccount::where('type', 'saving')->where('status', 'active')->first();
        $bankAccountInvestment = BankAccount::where('type', 'investment')->where('status', 'active')->first();

        $data = [];

        if ($bankAccountCharity) {
            $data['charity_account'] = new BankAccountResource($bankAccountCharity);
        } else {
            $data['charity_account'] = null;
        }

        if ($bankAccountSaving) {
            $data['saving_account'] = new BankAccountResource($bankAccountSaving);
        } else {
            $data['saving_account'] = null;
        }
        if ($bankAccountInvestment) {
            $data['investment_account'] = new BankAccountResource($bankAccountInvestment);
        } else {
            $data['investment_account'] = null;
        }
        return $data;
    }
}
