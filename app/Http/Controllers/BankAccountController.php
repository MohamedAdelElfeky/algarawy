<?php

namespace App\Http\Controllers;

use App\Domain\Services\BankAccountService;
use App\Http\Requests\BankAccountRequest;
use App\Http\Resources\BankAccountResource;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{

    public function __construct(private BankAccountService $bankAccountService) {}

    /**
     * Display a listing of the bank accounts.
     */
    public function index()
    {
        $bankAccounts = $this->bankAccountService->getAllBankAccounts();
        return BankAccountResource::collection($bankAccounts);
    }

    /**
     * Store a newly created bank account.
     */
    public function store(BankAccountRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $bankAccount = $this->bankAccountService->createBankAccount($data);

        return response()->json([
            'message' => 'Bank account created successfully',
            'data' => new BankAccountResource($bankAccount)
        ], 201);
    }

    /**
     * Display the specified bank account.
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
     * Update the specified bank account.
     */
    public function update(BankAccountRequest $request, string $id)
    {
        $data = $request->validated();

        $bankAccount = $this->bankAccountService->updateBankAccount($id, $data);

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], 404);
        }

        return new BankAccountResource($bankAccount);
    }

    /**
     * Remove the specified bank account.
     */
    public function destroy(string $id)
    {
        if ($this->bankAccountService->deleteBankAccount($id)) {
            return response()->json(['message' => 'Bank account deleted successfully']);
        }

        return response()->json(['message' => 'Bank account not found'], 404);
    }

    /**
     * Display charity and saving bank accounts.
     */
    public function accountCharitySaving()
    {
        $banks = $this->bankAccountService->getAccountsByType(['charity', 'saving']);
        return view('pages.dashboards.bank.account_charity_saving', compact('banks'));
    }

    /**
     * Display investment bank accounts.
     */
    public function accountInvestment()
    {
        $banks = $this->bankAccountService->getAccountsByType(['investment']);
        return view('pages.dashboards.bank.account_investment', compact('banks'));
    }

    /**
     * Activate a bank account.
     */
    public function activate($id)
    {
        if ($this->bankAccountService->changeStatus($id, 'active')) {
            return response()->json(['message' => 'تم تفعيل الحساب بنجاح']);
        }

        return response()->json(['message' => 'Bank account not found'], 404);
    }

    /**
     * Deactivate a bank account.
     */
    public function deactivate($id)
    {
        if ($this->bankAccountService->changeStatus($id, 'inactive')) {
            return response()->json(['message' => 'تم إلغاء تنشيط الحساب بنجاح']);
        }

        return response()->json(['message' => 'Bank account not found'], 404);
    }
}
