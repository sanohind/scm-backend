<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubcontReviewTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role == 4|9;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            "data.*.sub_transaction_id"=> "required|int",
            "data.*.sub_item_id"=> "required|int",
            "data.*.delivery_note"=> "required|string|max:255",
            "data.*.part_number"=> "required|string|max:50",
            "data.*.status"=> "required|string|in:Fresh,Replating",
            "data.*.actual_qty_ok"=> "integer|min:0",
            "data.*.actual_qty_ng"=> "integer|min:0",
            "data.*.response"=> "string|max:25"
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            // sub_tranasction_id
            'data.*.sub_transaction_id.required' => 'Transaction ID is required',
            'data.*.sub_transaction_id.int' => 'Transaction ID must be an integer',

            // sub_item_id
            'data.*.sub_item_id.required' => 'Item ID is required',
            'data.*.sub_item_id.int' => 'Item ID must be an integer',

            // delivery_note
            'data.*.delivery_note.required' => 'Delivery note is required',
            'data.*.delivery_note.string' => 'Delivery note must be a string',
            'data.*.delivery_note.max' => 'Delivery note must not exceed 255 characters',

            // part_number
            'data.*.part_number.required' => 'Part number is required',
            'data.*.part_number.string' => 'Part number must be a string',
            'data.*.part_number.max' => 'Part number must not exceed 50 characters',

            // status
            'data.*.status.required' => 'Status is required',
            'data.*.status.string' => 'Status must be a string',
            'data.*.status.in' => 'Status must be either Fresh or Replating',

            // actual_qty_ok
            'data.*.actual_qty_ok.integer' => 'OK quantity must be an integer',
            'data.*.actual_qty_ok.min' => 'OK quantity must not be negative',

            // actual_qty_ng
            'data.*.actual_qty_ng.integer' => 'NG quantity must be an integer',
            'data.*.actual_qty_ng.min' => 'NG quantity must not be negative',

            // response
            'data.*.response.string' => 'Response must be a string',
            'data.*.response.max' => 'Response must not exceed 25 characters'
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
