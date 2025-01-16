<?php

namespace App\Http\Requests;

use App\Models\Subcontractor\SubcontTransaction;
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
            "data.*.actual_qty_ok"=> "integer|min:0",
            "data.*.actual_qty_ng"=> "integer|min:0",
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

            // actual_qty_ok
            'data.*.actual_qty_ok.integer' => 'OK quantity must be an integer',
            'data.*.actual_qty_ok.min' => 'OK quantity must not be negative',

            // actual_qty_ng
            'data.*.actual_qty_ng.integer' => 'NG quantity must be an integer',
            'data.*.actual_qty_ng.min' => 'NG quantity must not be negative',
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

    // Validation
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // CheckMinValue
            $this->checkMinValue($validator);
        });
    }

    private function checkMinValue($validator) {
        $getRequest = $this->input('data');

        foreach ($getRequest as $data) {
            $getSupplierValue = SubcontTransaction::select('qty_ok','qty_ng')
            ->where('sub_transaction_id', $data['sub_transaction_id'])
            ->get();

            if ($data['actual_qty_ok'] > $getSupplierValue->qty_ok) {
                $validator->errors()->add(
                    "data.*.actual_qty_ok",
                    "The actual OK quantity cannot be greater than the supplier's OK quantity."
                );
            }

            if ($data['actual_qty_ng'] > $getSupplierValue->qty_ng) {
                $validator->errors()->add(
                    "data.*.actual_qty_ng",
                    "The actual NG quantity cannot be greater than the supplier's NG quantity."
                );
            }
        }
    }
}
