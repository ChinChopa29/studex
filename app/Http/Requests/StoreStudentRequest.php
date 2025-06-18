<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'surname' => 'required',
            'lastname' => 'required',
            'iin' => 'required|size:12',
            'phone' => 'required',
            'gender' => 'required|in:Мужской,Женский',
            'birthday' => 'required|date',
            'admission_year' => 'required|digits:4',
            'graduation_year' => 'required|digits:4',
            'education_program_id' => 'required|exists:education_programs,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле "Имя" обязательно.',
            'surname.required' => 'Поле "Фамилия" обязательно.',
            'lastname.required' => 'Поле "Отчество" обязательно.',
            'iin.required' => 'Поле "ИИН" обязательно.',
            'iin.size' => 'ИИН должен содержать 12 цифр.',
            'phone.required' => 'Поле "Телефон" обязательно.',
            'gender.required' => 'Выберите пол.',
            'birthday.required' => 'Введите дату рождения.',
            'birthday.date' => 'Дата рождения должна быть корректной.',
            'admission_year.required' => 'Введите год поступления.',
            'admission_year.digits' => 'Год поступления должен содержать 4 цифры.',
            'graduation_year.required' => 'Введите год окончания.',
            'graduation_year.digits' => 'Год окончания должен содержать 4 цифры.',
            'education_program_id.required' => 'Выберите образовательную программу.',
            'education_program_id.exists' => 'Выбранная образовательная программа не существует.',
        ];
    }
}
