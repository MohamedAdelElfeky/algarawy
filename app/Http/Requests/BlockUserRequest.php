<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'blocked_user_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'blocked_user_id.required' => 'معرف المستخدم المحظور مطلوب.',
            'blocked_user_id.exists' => 'المستخدم المحدد غير موجود.',
        ];
    }
}
