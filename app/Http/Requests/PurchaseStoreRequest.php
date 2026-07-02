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
            'update_id' => 'nullable|exists:purchases,id',
            'voucher_no' => 'required|string|max:50',
            'product_id' => 'required|integer|exists:products,id',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'vehicle_no' => 'required|string|max:20',
            'date' => 'required|date|before_or_equal:today',
            // 'rate_date' => 'required|date_format:Y-m-d',
            'crate_qty' => 'required|decimal:0,2|min:0|max:99999999',
            'total_weight' => 'required|numeric|decimal:0,2|min:0|max:99999999.99',
            'weight_cut' => 'required|numeric|decimal:0,2|min:0|max:99999999.99',
            'netweight' => 'required|numeric|decimal:0,2|min:0|max:99999999.99',

            // Made explicitly nullable as requested
            'rate' => 'nullable|numeric|decimal:0,2|min:0|max:99999999.99',

            // Optional: required_with ensures if a rate exists, total_amount must also exist
            'total_amount' => 'nullable|required_with:rate|numeric|min:0',
        ];
    }
}
