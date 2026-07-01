<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        dd($this->all());

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'voucher_no' => 'required|string|max:50',
            'product_id' => 'required|integer|exists:products,id',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'vehicle_no' => 'required|string|max:20',
            'date' => 'required|date|before_or_equal:today',
            // 'rate_date' => 'required|date_format:Y-m-d',
            'crate_qty' => 'required|integer|min:0',
            'total_weight' => 'required|numeric|min:0',
            'weight_cut' => 'required|numeric|min:0',
            'netweight' => 'required|numeric|min:0',

            // Made explicitly nullable as requested
            'rate' => 'nullable|numeric|min:0',

            // Optional: required_with ensures if a rate exists, total_amount must also exist
            'total_amount' => 'nullable|required_with:rate|numeric|min:0',
        ];
    }
}
