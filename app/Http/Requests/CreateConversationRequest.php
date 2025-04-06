<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:group,private,public',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

        ];
    }

    public function messages(): array
    {
        return [];
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
}
