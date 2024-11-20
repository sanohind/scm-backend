<?php

namespace App\Http\Requests;

use App\Models\SubcontItem;
use Illuminate\Support\Facades\Auth;
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
        return Auth::user()->role == 5;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            "transaction_type"=> "required|string|in:In,Out,Process",
            "item_code"=> "required|string|max:50",
            "status"=> "required|string|in:Fresh,Replating",
            "qty_ok"=> "integer|min:0",
            "qty_ng"=> "integer|min:0",
        ];

        ($this->transaction_type !== 'Process') ? $rules["delivery_note"] = "required|string|max:255" : $rules["delivery_note"] = "nullable|string|max:255";

        return $rules;
    }

    public function messages(): array
    {
        return [
            // Delivery Note
            "delivery_note.required" => "The delivery note is required.",
            "delivery_note.string" => "The delivery note must be a valid string.",
            "delivery_note.max" => "The delivery note length max 255.",

            // Transaction Type
            "transaction_type.required" => "The transaction type is required.",
            "transaction_type.string" => "The transaction type must be a valid string.",
            "transaction_type.in" => "The transaction type must be 'In', 'Out', or 'Process' .",

            // Item Code
            "item_code.required" => "The item_code is required.",
            "item_code.string" => "The item_code must be a valid string.",
            "item_code.max" => "The item_code length max 50.",

            // Status
            "status.required" => "The status is required.",
            "status.string" => "The status must be a valid string.",
            "status.in" => "The status must be either 'Fresh' or 'Replating'.",

            // Quantity OK
            "qty_ok.integer" => "The quantity OK must be an integer.",
            "qty_ok.min" => "The quantity OK must be at least 0.",

            // Quantity NG
            "qty_ng.integer" => "The quantity NG must be an integer.",
            "qty_ng.min" => "The quantity NG must be at least 0.",
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
                $validator->errors()->add('item_code', 'You do not have ownership of this item.');
            }
        });
    }

    private function ownership()
    {
       return SubcontItem::where("item_code", $this->item_code)
       ->where("bp_code", Auth::user()->bp_code)
       ->exists();

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
