<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class upsert extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'media_id' => 'nullable|integer|exists:media,id', 
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ];
    }
}
