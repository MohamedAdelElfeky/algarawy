<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'             => 'required|string|max:50',
            'middle_name'            => 'nullable|string|max:50',
            'last_name'              => 'required|string|max:50',
            'personal_title'         => 'nullable|string|max:20',
            'email'                  => 'required|email|unique:users,email',
            'phone'                  => 'required|string|unique:users,phone',
            'password'               => 'required|string|min:6',
            // 'password_confirmation' => 'required|string|same:password',
            'national_id'            => 'required|string|unique:users,national_id',
            'occupation_category'    => 'nullable|string|max:100',
            'is_whatsapp'            => 'nullable|boolean',
            'birth_date'             => 'nullable|date',
            'region_id'              => 'nullable|exists:regions,id',
            'city_id'                => 'nullable|exists:cities,id',
            'neighborhood_id'        => 'nullable|exists:neighborhoods,id',
            'device_id'              => 'required|string|max:255',
            'notification_token'     => 'required|string|max:255',

            // صور (اختياري)
            'avatar'                    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'national_card_image_front' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'national_card_image_back' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'card_images.*'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'message' => 'خطأ في التحقق من البيانات',
            'errors' => $validator->errors()->first(),
        ], 422));
    }
    public function messages()
    {
        return [];
    }
}
