<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Teacher extends Authenticatable
{
    protected $fillable = [
        'name', 'surname', 'lastname', 'iin', 'phone', 'gender', 'birthday',
        'email', 'password', 'image',
    ];

    public function courses() 
    {
        return $this->belongsToMany(Course::class, 'teacher_course');
    }

    public function groups() 
    {
        return $this->belongsToMany(Group::class, 'teacher_group');
    }

    public function getType()
    {
        return $this instanceof Teacher ? 'teacher' : 'student';
    }
}
