<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class JobRequest extends FormRequest
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
            'description' => 'nullable',
            'title' => 'nullable',
            'company_name' => 'nullable|string',
            'company_location' => 'nullable|string',
            'company_type' => 'nullable|string',
            'company_link' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'job_type' => 'nullable|string',
            'job_duration' => 'nullable',
            'price' => 'nullable|numeric',
            'job_status' => 'nullable|boolean',
            'images_or_video.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4',
            'files.*' => 'nullable|file',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'company_region_id' => 'nullable|exists:regions,id',
            'company_city_id' => 'nullable|exists:cities,id',
            'company_neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'is_training' => 'nullable',
            'status'      => 'nullable|in:public,private',
            'deleted_images_or_video' => 'nullable|array',
            'deleted_files' => 'nullable|array',
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

    public function messages()
    {
        return [];
    }
}
