<?php

namespace App\Http\Requests\Subcontractor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class SubcontItemUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role == 4 || 9;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sub_item_id' => 'required',
            'part_number' => 'string|max:50',
            'part_name' => 'string|max:255',
            'old_part_name' => 'string|max:255',
            'status' => 'string|in:1,0',
            'min_stock_incoming' => 'integer|min:0',
            'min_stock_outgoing' => 'integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            // sub_item_id
            'sub_item_id.required' => 'The sub item ID is required.',

            // part_number
            'part_number.string' => 'The part number must be a valid string.',
            'part_number.max' => 'The part number cannot be longer than 50 characters.',

            // part_name
            'part_name.string' => 'The part name must be a valid string.',
            'part_name.max' => 'The part name cannot be longer than 255 characters.',

            // old_part_name
            'old_part_name.string' => 'The old part name must be a valid string.',
            'old_part_name.max' => 'The old part name cannot be longer than 255 characters.',

            // status
            'status.string' => 'The status must be a valid string.',
            'status.in' => 'The status must be either 1 or 0.',

            // min_stock_incoming
            'min_stock_incoming.integer' => 'The minimum stock for incoming must be an integer.',
            'min_stock_incoming.min' => 'The minimum stock for incoming must be at least 0.',

            // min_stock_outgoing
            'min_stock_outgoing.integer' => 'The minimum stock for outgoing must be an integer.',
            'min_stock_outgoing.min' => 'The minimum stock for outgoing must be at least 0.',
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

    // Check if item_code already exist
    // Call the withValidation method (method injection from formRequest.php. it's core of framework method)
    // protected function withValidator($validator){
    //     $this->duplicateCheck($validator);
    // }

    // // Duplicate logic
    // private function duplicateCheck($validator): void {
    //     $data = SubcontItem::where('sub_item_id', $this->sub_item_id)
    //     ->where('item_code', $this->item_code)
    //     ->exists();

    //     $validator->after(function ($validator) use($data) {
    //         if ($data) {
    //             $validator->errors()->add('item_code', 'This Part Number already exist.');                }
    //         });
    // }
}
