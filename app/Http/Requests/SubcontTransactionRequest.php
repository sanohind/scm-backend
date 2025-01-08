<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use App\Models\Subcontractor\SubcontItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubcontTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role == 6 || 8 || 7 || 9;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            "bp_code"=> "string|max:25",
            "data.*.actual_transaction_date"=> "required|date",
            "data.*.actual_transaction_time"=> "date_format:H:i:s",
            "data.*.transaction_type"=> "required|string|in:Incoming,Outgoing,Process",
            "data.*.item_code"=> "required|string|max:50",
            "data.*.status"=> "required|string|in:Fresh,Replating",
            "data.*.qty_ok"=> "integer|min:0",
            "data.*.qty_ng"=> "integer|min:0",
        ];

        foreach ($this->input('data') as $type) {
            ($type['transaction_type'] !== 'Process') ? $rules["data.*.delivery_note"] = "required|string|max:255" : $rules["data.*.delivery_note"] = "nullable|string|max:255";
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            // Bp Code
            'bp_code.string' => 'The BP Code must be a string.',
            'bp_code.max' => 'The BP Code may not be greater than 25 characters.',

            // Delivery Note
            "data.*.delivery_note.required" => "The delivery note is required.",
            "data.*.delivery_note.string" => "The delivery note must be a valid string.",
            "data.*.delivery_note.max" => "The delivery note must not exceed 255 characters.",

            // Transaction Type
            "data.*.transaction_type.required" => "The transaction type is required.",
            "data.*.transaction_type.string" => "The transaction type must be a valid string.",
            "data.*.transaction_type.in" => "The transaction type must be one of the following: 'Incoming', 'Outgoing', or 'Process'.",

            // Item Code
            "data.*.item_code.required" => "The item code is required.",
            "data.*.item_code.string" => "The item code must be a valid string.",
            "data.*.item_code.max" => "The item code must not exceed 50 characters.",

            // Status
            "data.*.status.required" => "The status is required.",
            "data.*.status.string" => "The status must be a valid string.",
            "data.*.status.in" => "The status must be either 'Fresh' or 'Replating'.",

            // Quantity OK
            "data.*.qty_ok.integer" => "The quantity OK must be an integer.",
            "data.*.qty_ok.min" => "The quantity OK must be at least 0.",

            // Quantity NG
            "data.*.qty_ng.integer" => "The quantity NG must be an integer.",
            "data.*.qty_ng.min" => "The quantity NG must be at least 0.",
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

    // Check item_code ownership
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->ownership()) {
                $validator->errors()->add('data.*.item_code', 'You do not have ownership of this item.');
            }
        });
    }

    private function ownership()
    {
        foreach ($this->input('data') as $transaction) {
            SubcontItem::where("item_code", $transaction['item_code'])
                ->where("bp_code", Auth::user()->bp_code)
                ->exists();
        }

        return true;

    // Alternative if want to use this for message
    //    dd($check);
    //    if ($check == true) {
    //        return response()->json([
    //         "status"=> true,
    //         "message" => "Ownership Is True"
    //        ]);
    //    } else {
    //     return $this->failedAuthorization();
    //    }
    }

    // Alternative if want to use this for message
    // protected function failedAuthorization()
    // {
    //     throw new HttpResponseException(response()->json([
    //         'success' => false,
    //         'message' => 'You do not own the item. Please insert the right item code',
    //         'error_code' => 'OWNERSHIP_CHECK_FAILED'
    //     ], 403));
    // }

}
