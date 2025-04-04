<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
            'education_program_id' => 'required|exists:education_programs,id',
            'code' => 'string|unique:courses,code|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Название" обязательно',
            'credits.required' => 'Поле "Кредиты" обязательно',
            'semester.required' => 'Поле "Семестр" обязательно',
            'degree.required' => 'Поле "Степень" обязательно',
            'education_program_id.required' => 'Поле "Образовательная программа" обязательно',
            'education_program_id.exists' => 'Выбранная образовательная программа не существует',
            'type.required' => 'Поле "Тип" обязательно',
            'code.unique' => 'Этот код уже занят, выберите другой',
        ];
    }
}
