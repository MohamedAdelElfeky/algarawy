<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use App\Services\BankAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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


    public function accountCharitySaving()
    {
        $banks =  BankAccount::whereIn('type', ['charity', 'saving'])->paginate(25);
        return view('pages.dashboards.bank.account_charity_saving', compact('banks'));
    }
    public function accountInvestment()
    {
        $banks =  BankAccount::where('type', 'investment')->paginate(25);
        return view('pages.dashboards.bank.account_investment', compact('banks'));
    }

    public function activate($id)
    {
        $bank = BankAccount::findOrFail($id);
        $bank->status = 'active';
        $bank->save();

        return response()->json(['message' => 'تم تفعيل الحساب بنجاح']);
    }

    public function deactivate($id)
    {
        $bank = BankAccount::findOrFail($id);
        $bank->status = 'inactive';
        $bank->save();

        return response()->json(['message' => 'تم إلغاء تنشيط الحساب بنجاح']);
    }
}
