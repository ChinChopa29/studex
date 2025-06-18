<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'type', 'date', 'start_time', 'end_time', 'classroom_id',  'recurrence', 'recurrence_end_date', 'course_id', 'teacher_id', 'group_id', 'milestone_id','task_id',
    ];

    protected $casts = [
        'date' => 'date',
        'recurrence_end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function typeName()
    {
        $types = [
            'lecture' => 'Лекция',
            'practice' => 'Практика',
            'lab' => 'Лаб. работа',
            'seminar' => 'Семинар',
            'exam' => 'Экзамен',
            'consultation' => 'Консультация',
        ];
        
        return $types[$this->type] ?? 'Неизвестный тип';
    }

    const TYPE_LECTURE = 'lecture';
    const TYPE_PRACTICE = 'practice';
    const TYPE_LAB = 'lab';
    const TYPE_SEMINAR = 'seminar';
    const TYPE_EXAM = 'exam';
    
    const COLORS = [
        self::TYPE_LECTURE => '#3b82f6', // синий
        self::TYPE_PRACTICE => '#f59e0b', // желтый
        self::TYPE_LAB => '#10b981', // зеленый
        self::TYPE_SEMINAR => '#8b5cf6', // фиолетовый
        self::TYPE_EXAM => '#ef4444' // красный
    ];

    public function getColorAttribute()
    {
        return self::COLORS[$this->type] ?? '#6b7280'; // серый по умолчанию
    }

    public function materials()
    {
        return $this->hasMany(LessonMaterial::class, 'lesson_id'); 
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'lesson_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}