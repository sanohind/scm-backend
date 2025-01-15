<?php

namespace App\Http\Requests;


use Illuminate\Support\Facades\Auth;
use App\Models\Subcontractor\SubcontItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubcontItemRequest extends FormRequest
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
            "data.*.bp_code" => "required|string|max:50",
            "data.*.part_number"=> "required|string|max:50",
            "data.*.part_name"=> "required|string|max:255",
            "data.*.old_part_name"=> "required|string|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            // bp_code
            'data.*.bp_code.required' => 'The bp_code is required.',
            'data.*.bp_code.string' => 'The bp_code must be a valid string.',
            'data.*.bp_code.max' => 'The bp_code cannot be longer than 50 characters.',

            // part_number
            'data.*.part_number.required' => 'The part number is required.',
            'data.*.part_number.string' => 'The part number must be a valid string.',
            'data.*.part_number.max' => 'The part number cannot be longer than 50 characters.',

            // part_name
            'data.*.part_name.required' => 'The part name is required.',
            'data.*.part_name.string' => 'The part name must be a valid string.',
            'data.*.part_name.max' => 'The part name cannot be longer than 255 characters.',

            // old_part_name
            'data.*.old_part_name.required' => 'The  old part name is required.',
            'data.*.old_part_name.string' => 'The  old part name must be a valid string.',
            'data.*.old_part_name.max' => 'The  old part name cannot be longer than 255 characters.',
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

    // Check if part_number already exist
    // Call the withValidation method (method injection from formRequest.php. it's core of framework method)
    protected function withValidator($validator){
        $this->duplicateCheck($validator);
    }

    // Duplicate logic
    private function duplicateCheck($validator) {
        foreach ($this->input('data') as $item) {
            $data = SubcontItem::where('bp_code', $item['bp_code'])
            ->where('item_code', $item['part_number'])
            ->exists();

            $validator->after(function ($validator) use($data) {
                if ($data) {
                    $validator->errors()->add('data.*.part_number', 'This item code already exist.');
                }
            });
        }
    }
}

