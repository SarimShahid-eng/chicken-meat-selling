<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HotelSaleStoreRequest extends FormRequest
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
            'update_id' => 'nullable|exists:hotel_sales,id',
            'voucher_no' => 'required|string|max:50',
            'customer_id' => 'required|integer|exists:customers,id',
            'date' => 'required|date|before_or_equal:today',
            'items.*'=>'required|array|min:1',
            'items.*.product_id'=>'required|exists:products,id|distinct',
            'items.*.weight'=>'required|numeric|decimal:0,2|min:0|max:99999999.99',
            'items.*.rate'=>'required|numeric|decimal:0,2|min:0|max:99999999.99',
            'items.*.amount'=>'required|numeric|decimal:0,2|min:0|max:99999999.99',
            'amount_received'=>'nullable',
            'total_amount' => 'required|required_with:rate|numeric|min:0',
        ];
    }
}
