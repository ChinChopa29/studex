<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Message;
use App\Models\Student;
use App\Models\Task;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    public function login() {
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('CoursesIndex'); 
        }
    
        if (Auth::guard('student')->check()) {
            return redirect()->route('CoursesIndex'); 
        }

        return view('auth.auth');
    }

    public function authenticate()
    {
        $validated = request()->validate([
            'email' => 'required|min:3|max:50',
            'password' => 'required|min:5',
        ]);

        if ($admin = User::where('email', $validated['email'])->first()) {
            if (Hash::check($validated['password'], $admin->password)) {
                auth()->guard('admin')->login($admin);
                request()->session()->regenerate();
                return redirect()->route('admin.index');
            }
        }

        if ($teacher = Teacher::where('email', $validated['email'])->first()) {
            if (Hash::check($validated['password'], $teacher->password)) {
                auth()->guard('teacher')->login($teacher);
                request()->session()->regenerate();
                $messages = Message::all();
                return redirect()->route('CoursesIndex', compact('messages'));    
            }
        }

        if ($student = Student::where('email', $validated['email'])->first()) {
            if (Hash::check($validated['password'], $student->password)) {
                auth()->guard('student')->login($student);
                request()->session()->regenerate();
                $messages = Message::all();
                return redirect()->route('CoursesIndex', compact('messages'));
            }
        }

        
        return redirect()->route('login')->withErrors([
            'login' => 'Неверная почта или пароль'
        ]);
    }


    public function logout() {
        Auth::logout();
        session()->invalidate(); 
        session()->regenerateToken();  

        return redirect()->route('login');
    }

}
