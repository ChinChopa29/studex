<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:40',
            'admission_year' => 'required|min:4|max:5',
            'graduation_year' => 'required|min:4|max:5',
            'education_program_id' => 'required',
            'teacher' => 'nullable',
        ];
    }
}
