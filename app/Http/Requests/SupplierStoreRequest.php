<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
        return [
                'update_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255|unique:suppliers,name',
            'phone_number' => 'required|string|max:50|unique:suppliers,phone_number',
            'region_id' => 'required|integer|exists:regions,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
