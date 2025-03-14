<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageFile extends Model
{
    
    protected $fillable = ['message_id', 'file_path'];
    
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
