<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
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
            'total_weight' => 'required|numeric',
            'status' => 'nullable|string|in:received,processed,delivered',
            'order_products' => 'required|array',
            'order_products.*.product_id' => 'required|exists:products,id',
            'order_products.*.product_variant_id' => 'required|exists:product_variants,id',
            'order_products.*.quantity' => 'required|integer|min:1',
        ];
    }
}
