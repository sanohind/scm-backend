<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use App\Models\Subcontractor\SubcontItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubcontTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role == 6 || 8 || 7;
    }

    public function rules(): array
    {
        $userRole = Auth::user()->role;
        $rules = [
            'data'                         => 'required|array',
            'data.*.actual_transaction_date' => 'required|date',
            'data.*.actual_transaction_time' => 'nullable|date_format:H:i:s',
            'data.*.transaction_type'        => 'required|string|in:Incoming,Outgoing,Process',
            'data.*.item_code'               => 'required|string|max:50',
            'data.*.status'                  => 'required|string|in:Fresh,Replating',
            'data.*.qty_ok'                  => 'integer|min:0',
            'data.*.qty_ng'                  => 'integer|min:0',
        ];

        // Conditional validation for delivery_note
        foreach ($this->input('data') as $index => $type) {
            if ($type['transaction_type'] !== 'Process') {
                $rules["data.$index.delivery_note"] = 'required|string|max:255';
            } else {
                $rules["data.$index.delivery_note"] = 'nullable|string|max:255';
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            // BP Code
            'bp_code.required' => 'The bp_code is required for superusers.',
            'bp_code.string'   => 'The bp_code must be a valid string.',
            'bp_code.exists'   => 'The bp_code does not exist in subcont items.',

            // Delivery Note
            'data.*.delivery_note.required' => 'The delivery note is required.',
            'data.*.delivery_note.string'   => 'The delivery note must be a valid string.',
            'data.*.delivery_note.max'      => 'The delivery note must not exceed 255 characters.',

            // Transaction Type
            'data.*.transaction_type.required' => 'The transaction type is required.',
            'data.*.transaction_type.string'   => 'The transaction type must be a valid string.',
            'data.*.transaction_type.in'       => "The transaction type must be one of the following: 'Incoming', 'Outgoing', or 'Process'.",

            // Item Code
            'data.*.item_code.required' => 'The item code is required.',
            'data.*.item_code.string'   => 'The item code must be a valid string.',
            'data.*.item_code.max'      => 'The item code must not exceed 50 characters.',

            // Status
            'data.*.status.required' => 'The status is required.',
            'data.*.status.string'   => 'The status must be a valid string.',
            'data.*.status.in'       => "The status must be either 'Fresh' or 'Replating'.",

            // Quantity OK
            'data.*.qty_ok.integer' => 'The quantity OK must be an integer.',
            'data.*.qty_ok.min'     => 'The quantity OK must be at least 0.',

            // Quantity NG
            'data.*.qty_ng.integer' => 'The quantity NG must be an integer.',
            'data.*.qty_ng.min'     => 'The quantity NG must be at least 0.',
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
        $userRole = Auth::user()->role;
        $bp_code  = $userRole == 9 ? $this->route('bp_code') ?? $this->input('bp_code') : Auth::user()->bp_code;

        if (!$bp_code) {
            return false;
        }

        foreach ($this->input('data') as $transaction) {
            $ownsItem = SubcontItem::where('item_code', $transaction['item_code'])
                ->where('bp_code', $bp_code)
                ->exists();

            if (!$ownsItem) {
                return false;
            }
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
