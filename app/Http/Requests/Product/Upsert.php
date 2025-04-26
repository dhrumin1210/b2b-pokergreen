<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class Upsert extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $this->route('product'),
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ];

        if ($this->isMethod('patch')) {
            $rules['slug'] = 'required|string|max:255|unique:products,slug,' . $this->route('product');
        }

        return $rules;
    }
}
