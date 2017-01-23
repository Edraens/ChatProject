<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	protected $fillable=['fromUser', 'toUser', 'content', 'belongsTo', 'conversationId', 'unread'];
	
	public function sender(){
		return $this->belongsTo('App\User', 'fromUser', 'id');
	}

	public function conversation(){
		return $this->belongsTo('App\Conversation', 'conversationId', 'id');
	}
}
