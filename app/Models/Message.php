<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['receiver_id', 'receiver_type', 'sender_id', 'sender_type', 'message', 'type', 'status', 'deleted_by', 'deleted_by_receiver', 'deleted_by_sender'];

    public function receiver()
    {
        return $this->morphTo();
    }

    public function sender()
    {
        return $this->morphTo();
    }
    public function files()
    {
        return $this->hasMany(MessageFile::class, 'message_id', 'id');
    }
}
