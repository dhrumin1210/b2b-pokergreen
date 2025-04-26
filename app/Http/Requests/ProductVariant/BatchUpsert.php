<?php

namespace App\Http\Requests\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;

class BatchUpsert extends FormRequest
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
            'variants' => 'required|array|min:1',
            'variants.*.variant_id' => 'nullable|exists:product_variants,id',
            'variants.*.weight' => 'required|numeric|min:0.01',
            'variants.*.unit' => 'required|string|in:kg,gm,pc'
        ];
    }
}
