<?php

namespace App\Http\Requests\Subcontractor;

use App\Trait\AuthorizationRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubcontUpdateTransactionRequest extends FormRequest
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. AuthorizationRole = for checking permissible user role
     */
    use AuthorizationRole;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->permissibleRole('4','9');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "transaction_id" => 'required',
            "qty_ok" => 'sometimes|min:0',
            "qty_ng" => 'sometimes|min:0',
        ];
    }

    /**
     * Get the validation messages that apply to the rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "transaction_id.required" => "The transaction ID is required.",
            "qty_ok.min" => "The quantity OK must be at least 0.",
            "qty_ng.min" => "The quantity NG must be at least 0.",
        ];
    }

    // Failed validation response
    protected function failedValidation($validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Please Fill Input Field with Valid Data',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
