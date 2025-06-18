<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMilestoneRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from' => 'required|date',
            'deadline' => 'required|date|after_or_equal:from',
        ];
    }

    public function messages(): array
    {
        return [
            'from.date' => 'Дата начала должна быть корректной.',
            'deadline.date' => 'Дата дедлайна должна быть корректной.',
            'deadline.after_or_equal' => 'Дата дедлайна должна быть после даты начала.',
        ];
    }
}
