<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|min:3|max:50',
            'password' => 'required|min:5',
        ];
    }
}
