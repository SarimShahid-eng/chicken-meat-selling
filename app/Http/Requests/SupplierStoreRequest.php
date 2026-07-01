<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $updateId = $this->update_id;

        return [
            'update_id' => 'nullable|exists:suppliers,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('suppliers', 'name')->ignore($updateId),
            ],

            'phone_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('suppliers', 'phone_number')->ignore($updateId),
            ],
            'date' => 'required|date|before_or_equal:today',
            'region_id' => 'required|integer|exists:regions,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
