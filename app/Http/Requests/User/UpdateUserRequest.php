<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role == 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bp_code' => 'required|string|max:25',
            'name' => 'string|max:25',
            'role' => 'string|max:25',
            'password' => 'nullable|string|min:8',
            'username' => 'nullable|string|max:25', // username must unique
            'email' => 'email:rfc,strict|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bp_code.required' => 'Please provide the BP code.',
            'bp_code.string' => 'The BP code should be a valid string.',
            'bp_code.max' => 'The BP code should not exceed 25 characters.',
            'name.string' => 'The name should be a valid string.',
            'name.max' => 'The name should not exceed 25 characters.',
            'role.string' => 'The role should be a valid string.',
            'role.max' => 'The role should not exceed 25 characters.',
            'password.string' => 'The password should be a valid string.',
            'password.min' => 'The password should be at least 8 characters long.',
            'username.string' => 'The username should be a valid string.',
            'username.unique' => 'This username is already taken. Please choose another one.',
            'username.max' => 'The username should not exceed 25 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email address should not exceed 255 characters.',
        ];
    }

    // Failed validation response
    protected function failedValidation($validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'You Must Fill All Input Fields Correctly.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
