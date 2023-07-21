<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BankAccountService;
use Illuminate\Http\Request;

class ApiBankAccountController extends Controller
{
    protected $bankAccountService;

    public function __construct(BankAccountService $bankAccountService)
    {
        $this->bankAccountService = $bankAccountService;
    }

    public function index()
    {
        $bankAccounts = $this->bankAccountService->getAllBankAccounts();

        return response()->json(['data' => $bankAccounts]);
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

        return response()->json($bankAccount);
    }

    public function destroy($id)
    {
        $this->bankAccountService->deleteBankAccount($id);

        return response()->json(['message' => 'Bank account deleted successfully']);
    }
    
    public function getSavings()
    {
        $bankAccounts = $this->bankAccountService->getSavingBankAccounts();

        return response()->json(['data' => $bankAccounts]);
    }

    public function getCharities()
    {
        $bankAccounts = $this->bankAccountService->getCharityBankAccounts();

        return response()->json(['data' => $bankAccounts]);
    }
}
