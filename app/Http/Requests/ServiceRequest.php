<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
        return [
            'description' => 'required|string',
            'location' => 'nullable|string',
            'images_and_videos.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:10240',
            'deleted_images_and_videos' => 'nullable|array',
            'status' => 'nullable|in:public,private',
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
