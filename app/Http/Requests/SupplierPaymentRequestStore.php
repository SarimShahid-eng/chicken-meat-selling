<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierPaymentRequestStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // dd($this->all());
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
            'update_id' => 'nullable|exists:supplier_payments,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'required|in:bank,cash',
            'date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
