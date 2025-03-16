<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteMessage extends Model
{
    protected $fillable = ['user_id', 'user_type', 'message_id'];

    public function user() {
        return $this->morphTo();
    }
}
