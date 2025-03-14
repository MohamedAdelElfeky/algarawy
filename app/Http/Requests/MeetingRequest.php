<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MeetingRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'datetime'    => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'link'        => 'nullable|string',
            'name'        => 'required|string|max:255',
            'start_time'  => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'end_time'    => 'nullable|date_format:Y-m-d\TH:i:s.v|after_or_equal:start_time',
            'description' => 'nullable|string',
            'type'        => 'nullable|in:remotely,normal',
            'status'      => 'nullable|in:public,private',
        ];
    }

    public function messages()
    {
        return [];
    }

    /**
     * Handle validation failures and return JSON response.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'message' => 'خطأ في التحقق من البيانات',
            'errors' => $validator->errors()->first(),
        ], 422));
    }
}
