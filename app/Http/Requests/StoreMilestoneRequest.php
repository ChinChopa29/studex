<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMilestoneRequest extends FormRequest
{
    public function rules(): array
    {
        $courseId = $this->route('course')->id;

        return [
            'milestone_number' => [
                'required',
                'numeric',
                Rule::unique('milestones')->where(fn($query) => 
                    $query->where('course_id', $courseId)
                ),
            ],
            'from' => 'required|date',
            'deadline' => 'required|date|after_or_equal:from',
        ];
    }

    public function messages(): array
    {
        return [
            'milestone_number.required' => 'Номер рубежного контроля обязателен.',
            'milestone_number.numeric' => 'Номер рубежного контроля должен быть числом.',
            'milestone_number.unique' => 'Рубежный контроль с таким номером уже существует.',
            'from.date' => 'Дата начала должна быть корректной.',
            'deadline.date' => 'Дата дедлайна должна быть корректной.',
            'deadline.after_or_equal' => 'Дата дедлайна должна быть после даты начала.',
        ];
    }
}
