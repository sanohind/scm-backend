<?php

namespace App\Http\Requests\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBusinessPartnerEmailRequest extends FormRequest
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
            "email.*" => "email:rfc,strict",
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
            'email.*.email' => 'Each email must be a valid email address.',
        ];
    }

    // Failed validation response
    protected function failedValidation($validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Please fill in all required fields.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
