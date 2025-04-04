<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonMaterialRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file|max:10240',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ];
    }
}
