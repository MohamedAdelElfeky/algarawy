<?php

namespace App\Services;

use App\Domain\Models\BankAccount;
use App\Http\Resources\BankAccountResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BankAccountService
{
    public function getAllBankAccounts()
    {
        $bankAccounts = BankAccount::paginate(5);
        return BankAccountResource::collection($bankAccounts);
    }

    public function createBankAccount(array $data)
    {
        $validator = Validator::make($data, [
            'account_number' => 'required',
            'iban' => 'required',
            'bank_name' => 'required',
            'swift_number' => 'required',
            'type' => 'required|in:saving,charity,investment',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }
        $bankAccount = BankAccount::create($data);
        return [
            'message' => 'Bank account created successfully',
            'data' => new BankAccountResource($bankAccount)
        ];
    }

    public function getBankAccountById(string $id)
    {
        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return ['message' => 'Bank account not found'];
        }

        return new BankAccountResource($bankAccount);
    }

    public function updateBankAccount(string $id, array $data)
    {
        $validator = Validator::make($data, [
            'account_number' => 'required',
            'iban' => 'required',
            'bank_name' => 'required',
            'swift_number' => 'required',
            'type' => 'required|in:saving,charity,investment',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return ['message' => 'Bank account not found'];
        }

        $bankAccount->update($data);

        return [
            'message' => 'Bank account updated successfully',
            'data' => new BankAccountResource($bankAccount)
        ];
    }

    public function deleteBankAccount(string $id)
    {
        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], 404);
        }

        $bankAccount->delete();

        return ['message' => 'Bank account deleted successfully'];
    }

    public function getSavingBankAccounts()
    {
        $bankAccounts = BankAccount::where('type', 'saving')->paginate(5);
        return BankAccountResource::collection($bankAccounts);
    }

    public function getCharityBankAccounts()
    {
        $bankAccounts = BankAccount::where('type', 'charity')->paginate(5);
        return BankAccountResource::collection($bankAccounts);
    }


   
}
