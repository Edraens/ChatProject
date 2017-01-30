<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Token;
use Auth;
use App\User;
use App\Conversation;
use App\Message;
use \Input;

class ChatController extends Controller
{
	public function index(){
		if (!Auth::check()) return view('index');

		// $token = Token::where('userId', Auth::user()->id)->first();
		// if (is_null($token)) {
		// 	return redirect('/token/renew');
		// }
		$user = User::where('id', Auth::user()->id)->first();

		$conversations = Conversation::where('userId', Auth::user()->id)->get();
		$lastMessage = "";
		foreach ($conversations as $conversation) {
			$lastMessage[$conversation->id] = Message::where('conversationId', $conversation->id)->orderBy('created_at', 'desc')->first();
			if (empty($lastMessage[$conversation->id])) $lastMessage[$conversation->id] = "";
		}
		return view('conversationList', ['conversations' => $conversations, 'user' => $user, 'lastMessage' => $lastMessage]); // 'token' => $token->token, 
	}

	public function openConversation($email){
		if (!Auth::check()) return redirect('/login');
		$findDest = User::where('email', $email)->firstOrFail();
		if ($findDest->id == Auth::user()->id) return view('errors/404');
		
		$findConversation = Conversation::where([['userId', '=', Auth::user()->id], ['destId', '=', $findDest->id]])->first();
		if (is_null($findConversation)) {
			Conversation::create([
				'userId' => Auth::user()->id,
				'destId' => $findDest->id,
				'hasUnread' => false,
				'lastActivity' => time(),
				]);
			$findConversation = Conversation::where([['userId', '=', Auth::user()->id], ['destId', '=', $findDest->id]])->first();
		}
		return redirect('/conversation/'.$findConversation->id);
	}

	public function openConversationPOST(Request $request){
		$this->validate($request, [
			'email' => 'max:50|required|email',
			]);
		if (!Auth::check()) return redirect('/login');
		$findDest = User::where('email', Input::get('email'))->firstOrFail();
		if ($findDest->id == Auth::user()->id) return view('errors/404');
		
		$findConversation = Conversation::where([['userId', '=', Auth::user()->id], ['destId', '=', $findDest->id]])->first();
		if (is_null($findConversation)) {
			Conversation::create([
				'userId' => Auth::user()->id,
				'destId' => $findDest->id,
				'hasUnread' => false,
				'lastActivity' => time(),
				]);
			$findConversation = Conversation::where([['userId', '=', Auth::user()->id], ['destId', '=', $findDest->id]])->first();
		}
		return redirect('/conversation/'.$findConversation->id);
	}

	public function show($id){
		if (!Auth::check()) return redirect('/login');
		$conv = Conversation::where('id', $id)->firstOrFail();
		if ($conv->userId != Auth::user()->id) return view('errors/404');
		$messages = Message::where('conversationId', $conv->id)->orderBy('created_at', 'DESC')->paginate(10);
		$unreadMessages = Message::where([['conversationId', '=', $conv->id], ['unread', '=', true]])->get();
		foreach ($unreadMessages as $unreadMessage) {
			$unreadMessage->unread = false;
			$unreadMessage->save();
		}
		$conv->hasUnread = false;
		$conv->save();

		return view('conversation', ['conv' => $conv, 'messages' => $messages]);
	}

	public function send($id, Request $request){
		$this->validate($request, [
			'msg' => 'required',
			]);
		if (!Auth::check()) return redirect('/login');
		$conv = Conversation::where('id', $id)->firstOrFail();
		if ($conv->userId != Auth::user()->id) return view('errors/404');

		$correspConv = Conversation::where([['userId', '=', $conv->destUser->id], ['destId', '=', Auth::user()->id]])->first();
		if (is_null($correspConv)) {
			Conversation::create([
				'userId' => $conv->destUser->id,
				'destId' => Auth::user()->id,
				'hasUnread' => true,
				'lastActivity' => time(),
				]);
			$correspConv = Conversation::where([['userId', '=', $conv->destUser->id], ['destId', '=', Auth::user()->id]])->first();
		}
		else {
			$correspConv->hasUnread = true;
			$correspConv->save();
		}

		Message::create([
			'fromUser' => Auth::user()->id,
			'belongsTo' => Auth::user()->id,
			'toUser' => $conv->destUser->id,
			'content' => Input::get('msg'),
			'conversationId' => $conv->id,
			'unread' => false,
			]);

		Message::create([
			'fromUser' => Auth::user()->id,
			'belongsTo' => $conv->destUser->id,
			'toUser' => $conv->destUser->id,
			'content' => Input::get('msg'),
			'conversationId' => $correspConv->id,
			'unread' => true,
			]);

		return redirect('/conversation/'.$conv->id);
	}

	public function deleteConversation($id){
		if (!Auth::check()) return redirect('/login');
		$conversation = Conversation::where('id', $id)->firstOrFail();
		if ($conversation->userId != Auth::user()->id) return redirect('/login');
		$messages = Message::where('conversationId', $id);
		$messages->forceDelete();
		$conversation->forceDelete();
		return redirect('/');
	}

	public function deleteMessage($id){
		if (!Auth::check()) return redirect('/login');
		$message = Message::where('id', $id)->firstOrFail();
		$convId = $message->conversation->id;
		if ($message->belongsTo != Auth::user()->id) return redirect('/login');
		$message->forceDelete();
		return redirect('/conversation/'.$convId);
	}
}
