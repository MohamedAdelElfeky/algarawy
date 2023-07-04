<?php

namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BankAccountService
{
    public function getAllBankAccounts()
    {
        return BankAccount::all();
    }

    public function createBankAccount(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'account_number' => 'required',
            'iban' => 'required',
            'bank_name' => 'required',
            'swift_number' => 'required',
            'type' => 'required|in:saving,charity',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $bankAccount = BankAccount::create($data);

        return response()->json(['message' => 'Bank account created successfully', 'data' => $bankAccount]);
    }

    public function getBankAccountById(string $id): JsonResponse
    {
        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], 404);
        }

        return response()->json(['data' => $bankAccount]);
    }

    public function updateBankAccount(string $id, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'account_number' => 'required',
            'iban_number' => 'required',
            'bank_name' => 'required',
            'swift_number' => 'required',
            'type' => 'required|in:حساب ادخار,حساب صدقة',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], 404);
        }

        $bankAccount->update($data);

        return response()->json(['message' => 'Bank account updated successfully', 'data' => $bankAccount]);
    }

    public function deleteBankAccount(string $id): JsonResponse
    {
        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], 404);
        }

        $bankAccount->delete();

        return response()->json(['message' => 'Bank account deleted successfully']);
    }
}
