<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'name', 'milestone_number', 'from', 'deadline'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
