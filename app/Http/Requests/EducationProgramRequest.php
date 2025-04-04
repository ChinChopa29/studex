<?php

namespace App\Http\Requests;

use App\Rules\ValidDurationForDegree;
use Illuminate\Foundation\Http\FormRequest;

class EducationProgramRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|min:5|max:150',
                'acronym' => 'nullable',
                'description' => 'required|min:50|max:3000',
                'degree' => 'required|in:Бакалавриат,Магистратура,Аспирантура',
                'duration' => ['required', 'integer', new ValidDurationForDegree($this->input('degree'))],
                'mode' => 'required|in:Очная,Очно-заочная,Дистанционная',
                'price' => 'required|numeric|min:0',
        ];
    }
}
