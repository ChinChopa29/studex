<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|in:lecture,practice,lab,seminar,exam,consultation',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'group_id' => 'required|exists:groups,id',
            'classroom' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'task_id' => 'nullable|exists:tasks,id'
        ];
    }
}
