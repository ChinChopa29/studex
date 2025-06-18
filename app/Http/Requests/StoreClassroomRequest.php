<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassroomRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => 'required|string|max:255|unique:classrooms,number',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|string',
            'computers' => 'nullable|integer|min:0',
            'education_program_ids' => 'nullable|array',
            'education_program_ids.*' => 'exists:education_programs,id',
        ];
    }
}
