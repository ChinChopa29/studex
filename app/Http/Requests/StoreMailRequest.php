<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'receiver_id' => 'required',
            'receiver_type' => 'required|in:student,teacher,admin',
            'message' => 'required|string',
            'files.*' => 'nullable|file|max:2048', 
        ];
    }
}
