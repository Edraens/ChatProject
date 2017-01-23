<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	protected $fillable=['fromUser', 'toUser', 'content', 'belongsTo', 'conversationId', 'unread'];
}
