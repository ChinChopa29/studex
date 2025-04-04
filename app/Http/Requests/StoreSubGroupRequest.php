<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:groups,name',
            'group' => 'required', 
            'subgroup' => 'nullable', 
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Группа с таким названием уже существует. Пожалуйста, выберите другое имя.',
        ];
    }
}
