<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonMaterial extends Model
{
    protected $fillable = [
        'lesson_id', 'name', 'path', 'description',
    ];

    public function lesson()
    {
        return $this->belongsTo(Schedule::class, 'lesson_id'); // Явно указываем ключ
    }
}
