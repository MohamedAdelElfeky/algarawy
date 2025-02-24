<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account_number' => 'required|string|max:50',
            'iban' => 'required|string|max:34',
            'bank_name' => 'required|string|max:100',
            'swift_number' => 'required|string|max:20',
            'type' => 'required|in:saving,charity,investment',
        ];
    }
}
