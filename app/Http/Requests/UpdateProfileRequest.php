<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = auth()->id();
        return [
           'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $userId,
            'phone' => 'nullable|string|unique:users,phone,' . $userId,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'is_avatar_deleted' => 'nullable|boolean',
        ];
    }
}
