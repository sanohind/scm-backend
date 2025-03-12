<?php

namespace App\Http\Requests\PerformanceReport;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePerformanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role == 2 || 9;
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
            'date' => 'required|date',
            'file' => 'required|mimes:pdf|max:5000',
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
            'bp_code.required' => 'The BP code is required.',
            'bp_code.string' => 'The BP code must be a string.',
            'bp_code.max' => 'The BP code may not be greater than 25 characters.',
            'date.required' => 'The date is required.',
            'date.date' => 'The date is not a valid date format.',
            'file.required' => 'The file is required.',
            'file.mimes' => 'The file must be a PDF.',
            'file.max' => 'The file may not be greater than 5000 kilobytes.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Please Fill Input Field with Valid Data',
                'error' => $validator->errors(),
            ], 403)
        );
    }
}
