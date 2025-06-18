<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseStyle extends Model
{
    protected $fillable = [
        'color', 'student_id', 'course_id',
    ];
}
