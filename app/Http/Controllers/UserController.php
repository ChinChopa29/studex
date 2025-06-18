<?php

namespace App\Http\Controllers;

use App\Models\Student;

class UserController extends Controller
{
    public function studentProfile(Student $student) {
        return view('student-profile', compact('student'));
    }
}
