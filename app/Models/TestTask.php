<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'milestone_id', 'name', 'description', 'from', 'deadline', 'shuffle_questions'
    ];

    protected $casts = [
        'from' => 'datetime',
        'deadline' => 'datetime',
        'shuffle_questions' => 'boolean'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function questions()
    {
        return $this->hasMany(TestQuestion::class);
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }
}