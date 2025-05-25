<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfile extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:120',
            'mobile' => 'required|digits:10',
            'address' => 'required',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }
}