<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_task_id', 'student_id', 'score', 'correct_answers', 'total_questions', 'answers'
    ];

    protected $casts = [
        'answers' => 'array'
    ];

    public function testTask()
    {
        return $this->belongsTo(TestTask::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
