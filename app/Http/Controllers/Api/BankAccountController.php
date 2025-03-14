<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\BankAccountService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BankAccountResource;
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
        return response()->json(BankAccountResource::collection($bankAccounts), 200);
    }

    public function getCharities()
    {
        $bankAccounts = $this->bankAccountService->getCharityBankAccounts();
        return response()->json(BankAccountResource::collection($bankAccounts), 200);
    }

    public function getCharityAndSavingBankAccounts()
    {
        $accounts = $this->bankAccountService->getCharityAndSavingBankAccounts();

        return response()->json([
            'charity_account' => $accounts['charity_account'] ? new BankAccountResource($accounts['charity_account']) : null,
            'saving_account' => $accounts['saving_account'] ? new BankAccountResource($accounts['saving_account']) : null,
            'investment_account' => $accounts['investment_account'] ? new BankAccountResource($accounts['investment_account']) : null,
        ], 200);
    }
}
