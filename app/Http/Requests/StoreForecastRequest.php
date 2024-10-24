<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreForecastRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role == 3;
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
            'description' => 'required|string',
            'file' => 'required|mimes:pdf|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            // bp_code
            'bp_code.required' => 'The business partner code is required.',
            'bp_code.string' => 'The business partner code must be a string.',
            'bp_code.max' => 'The business partner code cannot exceed 25 characters.',

            // description
            'description.required' => 'The description is required.',
            'description.string' => 'The description must be a string.',

            // file
            'file.required' => 'A file is required.',
            'file.mimes' => 'The file must be a PDF.',
            'file.max' => 'The file size cannot exceed 5MB.',
        ];
    }

    // Failed validation response
    protected function failedValidation($validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
