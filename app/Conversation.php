<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable=['userId', 'destId', 'hasUnread', 'lastActivity'];

     public function user()
    {
        return $this->belongsTo('App\User', 'destId', 'id');
    }
}
