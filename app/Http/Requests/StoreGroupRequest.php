<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:groups,name',
            'admission_year' => 'required|digits:4',
            'education_program_id' => 'required|exists:education_programs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Группа с таким названием уже существует. Пожалуйста, выберите другое имя.',
            'admission_year.digits' => 'Год поступления должен состоять из четырех цифр.',
        ];
    }
}
