<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignUp extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:120',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
            'mobile' => 'required|digits:10',
            'address' => 'required|max:255',
        ];
    }
}