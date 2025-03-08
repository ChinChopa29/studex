<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['message', 'type', 'status'];

    public function receiver()
    {
        return $this->morphTo();
    }

    public function sender()
    {
        return $this->morphTo();
    }
}
