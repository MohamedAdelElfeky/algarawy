<?php

namespace App\Http\Requests;

use App\Domain\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Retrieve the user model from the route and load userDetails
        $user = $this->route('user');
        if ($user instanceof User) {
            $user->load('userDetails');
        }

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id ?? null),
            ],
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'phone')->ignore($user->id ?? null),
            ],
            'national_id' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'national_id')->ignore($user->id ?? null),
            ],
            'password' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'min:6'],

            'birthdate' => ['nullable', 'date'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'neighborhood_id' => ['nullable', 'integer', 'exists:neighborhoods,id'],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'national_card_image_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'national_card_image_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Array of additional images
            'card_images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422));
    }

    public function messages()
    {
        return [

        ];
    }
}
