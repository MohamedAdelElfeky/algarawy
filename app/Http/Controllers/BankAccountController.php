<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankAccountResource;
use App\Services\BankAccountService;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    protected $bankAccountService;

    public function __construct(BankAccountService $bankAccountService)
    {
        $this->bankAccountService = $bankAccountService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bankAccounts = $this->bankAccountService->getAllBankAccounts();
        return BankAccountResource::collection($bankAccounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $bankAccount = $this->bankAccountService->createBankAccount($data);

        return new BankAccountResource($bankAccount);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bankAccount = $this->bankAccountService->getBankAccountById($id);

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], 404);
        }

        return new BankAccountResource($bankAccount);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        $bankAccount = $this->bankAccountService->updateBankAccount($id, $data);

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], 404);
        }

        return new BankAccountResource($bankAccount);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = $this->bankAccountService->deleteBankAccount($id);

        if ($result) {
            return response()->json(['message' => 'Bank account deleted successfully']);
        }

        return response()->json(['message' => 'Bank account not found'], 404);
    }
}
