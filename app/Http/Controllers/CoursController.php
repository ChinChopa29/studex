<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\EducationProgram;
use App\Models\Teacher;
use Illuminate\Http\Request;

class CoursController extends Controller
{

    public function index() {
        $courses = Course::paginate(10);
        return view('admin.courses', compact('courses'));
    }

    public function show(Course $course) {
        $teachers = Teacher::all();
        return view('admin.show.course', compact('course', 'teachers'));
    }

    public function create() {
        $educationPrograms = EducationProgram::all();
        return view('admin.add.add-course', compact('educationPrograms'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:0',
            'semester' => 'required|integer|min:1|max:10',
            'type' => 'required|in:Обязательный,Элективный',
            'degree' => 'required|in:Бакалавриат,Магистратура,Аспирантура',
            'education_program_id' => 'required|exists:education_programs,id',
            'code' => 'string|unique:courses,code|max:50',
        ], [
            'name.required' => 'Поле "Название" обязательно',
            'credits.required' => 'Поле "Кредиты" обязательно',
            'semester.required' => 'Поле "Семестр" обязательно',
            'degree.required' => 'Поле "Степень" обязательно"',
            'education_program_id.required' => 'Поле "Образовательная программа" обязательно',
            'education_program_id.exists' => 'Выбранная образовательная программа не существует',
            'type.required' => 'Поле "Тип" обязательно',
            'code.unique' => 'Этот код уже занят, выберите другой',
        ]);

        $course = Course::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'credits' => $validated['credits'],
            'semester' => $validated['semester'],
            'type' => $validated['type'],
            'code' => $validated['code'],
        ]);

        $validEducationPrograms = array_map('intval', (array) $validated['education_program_id']);
        $course->educationPrograms()->sync($validEducationPrograms);

        return redirect()->route('admin.createCourse')->with('success', 'Курс успешно создан!');
    }

    public function searchCode(Request $request) {
        $code = $request->query('code');
    
        if (!$code) {
            return response()->json(['error' => 'Код не передан'], 400);
        }
    
        $courses = Course::where('code', 'like', "%$code%")->get();
    
        return response()->json($courses);
    }

    public function search(Request $request) {
    $query = $request->get('search');  
    $degree = $request->get('degree'); 
    $semester = $request->get('semester'); 

    $courses = Course::query();

    if ($query) {
        $courses->where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%$query%")
            ->orWhere('semester', 'LIKE', "%$query%")
            ->orWhere('degree', 'LIKE', "%$query%")
            ->orWhere('credits', 'LIKE', "%$query%") // Поиск по количеству кредитов
            ->orWhereHas('educationPrograms', function ($q) use ($query) {
                $q->where('title', 'LIKE', "%$query%");
            })
            ->orWhereHas('teachers', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                    ->orWhere('surname', 'LIKE', "%$query%")
                    ->orWhere('lastname', 'LIKE', "%$query%");
            });
        });
        }

        if ($degree) {
            $courses->where('degree', $degree);
        }

        if ($semester) {
            $courses->where('semester', $semester);
        }

        $courses = $courses->paginate(10)->appends(request()->query());

        return view('admin.courses', compact('courses'));
    }

    public function edit(Course $course) {
        $teachers = Teacher::all();
        $editing = true;
        return view('admin.show.course', compact('course', 'editing', 'teachers'));
    }

    public function update(Course $course, Request $request) {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'credits' => 'required|integer|min:0',
                'semester' => 'required|integer|min:1|max:10',
                'type' => 'required|in:Обязательный,Элективный',
                'degree' => 'required|in:Бакалавриат,Магистратура,Аспирантура',
                'code' => 'string|max:50',
                'teacher' => 'nullable',
            ]);

            if (!empty($validated['teacher'])) {
                $course->teachers()->attach($validated['teacher']);
            }
            
            $course->update($validated);
    
            return redirect()->route('admin.showCourse', $course->id)
                             ->with('success', 'Курс успешно обновлен');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Произошла ошибка. Пожалуйста, попробуйте снова.'])->withInput();
        }
    }

    public function detachTeacherCourse($courseId, $teacherId) {
        $course = Course::findOrFail($courseId);
        $course->teachers()->detach($teacherId);

        return redirect()->back()->with('success', 'Преподаватель успешно исключен из курса');
    }

    public function destroy(Course $course) {
        $course->delete();
        return redirect()->route('admin.showCourses')->with('success', 'Курс успешно удален');
    }
}
