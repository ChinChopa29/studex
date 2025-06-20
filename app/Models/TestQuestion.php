<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_task_id', 'text', 'shuffle_answers'
    ];

    protected $casts = [
        'shuffle_answers' => 'boolean'
    ];

    public function testTask()
    {
        return $this->belongsTo(TestTask::class);
    }

    public function answers()
    {
        return $this->hasMany(TestAnswer::class);
    }
}