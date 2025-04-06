<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $firstError = collect($validator->errors()->all())->first();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => 'حدث خطأ أثناء التسجيل، يرجى المحاولة مرة أخرى',
            'message' => $firstError,
        ], 422));
    }
    public function messages(): array
    {
        return [
            'blocked_user_id.required' => 'معرف المستخدم المحظور مطلوب.',
            'blocked_user_id.exists' => 'المستخدم المحدد غير موجود.',
        ];
    }
}
