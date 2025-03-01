<?php

namespace App\Http\Controllers;

use App\Models\EducationProgram;
use App\Models\Student;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function studentProfile(Student $student) {
        return view('student-profile', compact('student'));
    }
    // public function create() {
    //     $educationPrograms = EducationProgram::all();
    //     return view('admin.add.add-user', compact('educationPrograms'));
    // }

    // public function store(Request $request) {
    //     $query = $request->get('search');  
    //     $degree = $request->get('degree'); 
    //     $mode = $request->get('mode'); 
    //     $duration = $request->get('duration'); 

    //     $educationPrograms = EducationProgram::query();

    //     if ($query) {
    //         $educationPrograms->where('title', 'LIKE', "%$query%")
    //                         ->orWhere('degree', 'LIKE', "%$query%")
    //                         ->orWhere('mode', 'LIKE', "%$query%")
    //                         ->orWhere('duration', 'LIKE', "%$query%");
    //     }

    //     if ($degree) {
    //         $educationPrograms->where('degree', $degree);
    //     }
    //     if ($mode) {
    //         $educationPrograms->where('mode', $mode);
    //     }
    //     if ($duration) {
    //         $educationPrograms->where('duration', $duration);
    //     }

    //     $educationPrograms = $educationPrograms->get();

    //     return view('admin.add.add-user', compact('educationPrograms'));
    // }
}
