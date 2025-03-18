<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name'       => 'nullable|string',
            'middle_name'       => 'nullable|string',
            'last_name'        => 'nullable|string',
            'email'            => 'nullable|email|unique:users,email,' . $this->user->id,
            'phone'            => 'nullable|string|unique:users,phone,' . $this->user->id,
            'avatar'           => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'is_avatar_deleted' => 'nullable|boolean',
        ];
    }
}
