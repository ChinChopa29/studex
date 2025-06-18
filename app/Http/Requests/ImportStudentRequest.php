<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'students' => 'required|file|mimes:csv,txt',
            'education_program_id' => 'required|exists:education_programs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'students.required' => 'Выберите CSV файл.',
            'students.file' => 'Загруженный файл недопустим.',
            'students.mimes' => 'Файл должен быть формата CSV или TXT.',
            'education_program_id.required' => 'Выберите образовательную программу.',
            'education_program_id.exists' => 'Выбранная образовательная программа не найдена.',
        ];
    }
}
