<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use App\Models\Subcontractor\SubcontItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            "sub_item_id" =>"required",
            "item_code" =>"string|max:50",
            "item_name" =>"string|max:255",
            "status" =>"string|in:1,0",
        ];
    }

    public function messages(): array
    {
        return [
            // sub_item_id
            'sub_item_id.required' => 'The sub item ID is required.',

            // item_code
            'item_code.string' => 'The item code must be a valid string.',
            'item_code.max' => 'The item code cannot be longer than 50 characters.',

            // item_name
            'item_name.string' => 'The item name must be a valid string.',
            'item_name.max' => 'The item name cannot be longer than 255 characters.',

            // status
            'status.string' => 'The status must be a valid string.',
            'status.in' => 'The status must be either 1 or 0.',
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

    // Check if item_code already exist
    // Call the withValidation method (method injection from formRequest.php. it's core of framework method)
    protected function withValidator($validator){
        $this->duplicateCheck($validator);
    }

    // Duplicate logic
    private function duplicateCheck($validator): void {
        $data = SubcontItem::where('sub_item_id', $this->sub_item_id)
        ->where('item_code', $this->item_code)
        ->exists();

        $validator->after(function ($validator) use($data) {
            if ($data) {
                $validator->errors()->add('item_code', 'This Part Number already exist.');                }
            });
    }
}
