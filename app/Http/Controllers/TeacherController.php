<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function create() {
        return view('admin.add.add-teacher');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'lastname' => 'required',
            'iin' => 'required|size:12',
            'phone' => 'required',
            'gender' => 'required|in:Мужской,Женский',
            'birthday' => 'required|date',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'email' => 'required|email|unique:teachers,email',
            'password' => 'required|min:5',
        ], [
            'name.required' => 'Поле "Имя" обязательно',
            'surname.required' => 'Поле "Фамилия" обязательно',
            'lastname.required' => 'Поле "Отчество" обязательно',
            'iin.required' => 'Поле "ИИН" обязательно',
            'iin.size' => 'ИИН должен содержать 12 цифр',
            'phone.required' => 'Поле "Телефон" обязательно',
            'gender.required' => 'Выберите пол',
            'birthday.required' => 'Введите дату рождения',
            'birthday.date' => 'Дата рождения должна быть корректной',
            'image.required' => 'Загрузите фотографию преподавателя',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Файл должен быть формата: jpeg, png, jpg, webp',
            'image.max' => 'Максимальный размер файла: 2MB',
            'email.required' => 'Введите корпоративную почту',
            'email.unique' => 'Данная почта уже занята',
            'password.required' => 'Придумайте пароль',
            'password.min' => 'Пароль должен содержать минимум 5 символов',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('teachers', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['password'] = Hash::make($validated['password']);

        Teacher::create($validated);

        return redirect()->route('admin.createTeacher')->with('success', 'Преподаватель успешно добавлен!');
    }

    public function index() {
        $teachers = Teacher::paginate(10);
        return view('admin.teachers', compact('teachers'));
    }

    public function show(Teacher $teacher) {
        $editing = false;
        return view('admin.show.teacher', compact('teacher', 'editing'));
    }

    public function edit(Teacher $teacher) {
        $editing = true;
        return view('admin.show.teacher', compact('teacher', 'editing'));
    }

    public function update(Teacher $teacher, Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'lastname' => 'required',
            'iin' => 'required|size:12',
            'phone' => 'required',
            'gender' => 'required|in:Мужской,Женский',
            'birthday' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id, 
            'password' => 'nullable|min:5', 
        ], [
            'name.required' => 'Поле "Имя" обязательно',
            'surname.required' => 'Поле "Фамилия" обязательно',
            'lastname.required' => 'Поле "Отчество" обязательно',
            'iin.required' => 'Поле "ИИН" обязательно',
            'iin.size' => 'ИИН должен содержать 12 цифр',
            'phone.required' => 'Поле "Телефон" обязательно',
            'gender.required' => 'Выберите пол',
            'birthday.required' => 'Введите дату рождения',
            'birthday.date' => 'Дата рождения должна быть корректной',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Файл должен быть формата: jpeg, png, jpg, webp',
            'image.max' => 'Максимальный размер файла: 2MB',
            'email.required' => 'Введите корпоративную почту',
            'email.unique' => 'Данная почта уже занята',
            'password.min' => 'Пароль должен содержать минимум 5 символов',
        ]);
    
        if ($request->hasFile('image')) {
            if ($teacher->image) {
                Storage::disk('public')->delete($teacher->image);
            }
    
            $imagePath = $request->file('image')->store('teachers', 'public');
            $validated['image'] = $imagePath;
        }
    
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); 
        }

        if (!$request->hasFile('image')) {
            $validated['image'] = $teacher->image;
        }
    
        $teacher->update($validated);
    
        return redirect()->back()->with('success', 'Преподаватель успешно обновлен!');
    }
    
    public function destroy(Teacher $teacher) {
        $teacher->delete();
        return redirect()->route('admin.showTeachers')->with('success', 'Преподаватель успешно удален');
    }

    public function search(Request $request) {
        $query = $request->get('search');
        $teachers = Teacher::query();
    
        if ($query) {
            $teachers->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                  ->orWhere('surname', 'LIKE', "%$query%")
                  ->orWhere('lastname', 'LIKE', "%$query%")
                  ->orWhere('iin', 'LIKE', "%$query%")
                  ->orWhere('email', 'LIKE', "%$query%");
            });
        }
        
        $teachers = $teachers->paginate(10)->appends(request()->query());
        
        return view('admin.teachers', compact('teachers'));
    }
}
