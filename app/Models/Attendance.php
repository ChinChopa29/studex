<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'lesson_id', 
        'student_id',
        'group_id',
        'status',
        'comment'
    ];

    public function lesson()
    {
        return $this->belongsTo(Schedule::class, 'lesson_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function statusName()
    {
        return [
            'present' => 'Присутствовал',
            'absent' => 'Отсутствовал',
            'late' => 'Опоздал'
        ][$this->status] ?? $this->status;
    }
}