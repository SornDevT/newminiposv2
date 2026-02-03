<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockImportRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'cost_price_per_unit' => 'required|numeric|min:0',
            'new_selling_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'supplier_id' => 'required|exists:suppliers,id',
            // Assuming import_date and user_id will be handled by the controller
            // Assuming supplier_id and invoice_number might be optional or generated
        ];
    }
}
