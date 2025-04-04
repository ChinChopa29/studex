<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'credits' => 'required|integer|min:0',
                'semester' => 'required|integer|min:1|max:10',
                'type' => 'required|in:Обязательный,Элективный',
                'degree' => 'required|in:Бакалавриат,Магистратура,Аспирантура',
                'code' => 'string|max:50',
                'teacher' => 'nullable',
        ];
    }
}
