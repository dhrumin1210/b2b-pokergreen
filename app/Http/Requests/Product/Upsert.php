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
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $this->route('product'),
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'media_id' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'media_id.exists' => 'The selected media is invalid.',
        ];
    }
}
