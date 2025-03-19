<?php

namespace App\Http\Requests\DeliveryNote;

use App\Models\DeliveryNote\DnDetail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateDeliveryNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role == 5 || Auth::user()->role == 6 || Auth::user()->role == 7 || Auth::user()->role == 9 || Auth::user()->role == 8;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'no_dn' => 'required|string',
            'updates' => 'required|array',
            'updates.*.dn_detail_no' => 'required|integer|exists:dn_detail,dn_detail_no',
            'updates.*.qty_confirm' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'no_dn.required' => 'Delivery note number is required',
            'no_dn.string' => 'Delivery note number must be a string',
            'updates.required' => 'Updates data is required',
            'updates.array' => 'Updates must be an array',
            'updates.*.dn_detail_no.required' => 'Delivery note detail number is required',
            'updates.*.dn_detail_no.integer' => 'Delivery note detail number must be an integer',
            'updates.*.dn_detail_no.exists' => 'Delivery note detail number does not exist',
            'updates.*.qty_confirm.required' => 'Quantity confirmation is required',
            'updates.*.qty_confirm.integer' => 'Quantity confirmation must be an integer',
            'updates.*.qty_confirm.min' => 'Quantity confirmation must be at least 0',
        ];
    }

    // Failed validation response
    protected function failedValidation($validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Request Validation is Invalid, Please Fill Input Field with Valid Data',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->checkQuantity($validator);
        });
    }

    // Check qty_confirm
    protected function checkQuantity($validator)
    {
        $getRequestUpdate = $this->input('updates');

        foreach ($getRequestUpdate as $i) {
            $getData = DnDetail::select('dn_qty', 'receipt_qty', 'dn_snp', 'qty_confirm')
                ->where('dn_detail_no', $i['dn_detail_no'])
                ->first();

            if ($getData) {
                $currentReceipt = (int) ($getData->receipt_qty ?? 0);
                $dnQty = (int) $getData->dn_qty;
                $dnSnp = (int) $getData->dn_snp;

                // Debugging
                // \Log::info("Validating DN Detail No: {$i['dn_detail_no']}", [
                //     'qty_confirm' => $i['qty_confirm'],
                //     'currentReceipt' => $currentReceipt,
                //     'dnQty' => $dnQty,
                //     'dnSnp' => $dnSnp,
                // ]);

                // Check qty_confirm must be multiple of dn_snp
                // if (($i['qty_confirm'] % $dnSnp) != 0) {
                //     $validator->errors()->add(
                //         "updates.{$i['dn_detail_no']}.qty_confirm",
                //         "Qty Confirm must be multiple of Qty Label"
                //     );
                // }

                // Check qty_confirm can't exceed qty_requested
                // if (($i['qty_confirm'] + $currentReceipt) > $dnQty) {
                //     $validator->errors()->add(
                //         "updates.{$i['dn_detail_no']}.qty_confirm",
                //         "Qty Confirm exceeds Qty Requested for DN: {$i['dn_detail_no']}"
                //     );
                // }

                // Check if dn_qty equals qty_confirm
                if ($getData->dn_qty == $getData->qty_confirm) {
                    if ($i['qty_confirm'] != 0) {
                        $validator->errors()->add(
                            "updates.{$i['dn_detail_no']}.qty_confirm",
                            "Qty Confirm must be 0 when DN Qty equals Qty Confirm for DN: {$i['dn_detail_no']}"
                        );
                    }
                }
            } else {
                $validator->errors()->add(
                    "updates.{$i['dn_detail_no']}.qty_confirm",
                    "DN Detail not found for DN: {$i['dn_detail_no']}"
                );
            }
        }
    }
}
