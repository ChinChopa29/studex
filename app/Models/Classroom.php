<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'capacity', 'type', 'computers',
    ];

    public function educationPrograms()
    {
        return $this->belongsToMany(EducationProgram::class, 'education_program_classroom');
    }
    
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'classroom_id');
    }
}
