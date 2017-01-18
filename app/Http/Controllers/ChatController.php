<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Token;
use Auth;
use App\User;
use App\Conversation;

class ChatController extends Controller
{
	public function index(){
		if (!Auth::check()) return view('index');

		$token = Token::where('userId', Auth::user()->id)->first();
		if (is_null($token)) {
			return redirect('/token/renew');
		}
		$user = User::where('id', Auth::user()->id)->first();

		$conversations = Conversation::where('userId', Auth::user()->id)->get();
		$nameToConv = [];
		foreach ($conversations as $conversation){
			$nameToConv[$conversation->id] = Conversation::where('userId', Auth::user()->id)->first()->user->email;
		}

		return view('conversationList', ['conversations' => $conversations, 'nameToConv' => $nameToConv, 'token' => $token->token, 'user' => $user]);
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

	public function show($id){
		if (!Auth::check()) return redirect('/login');
		$conv = Conversation::where('id', $id)->firstOrFail();
		if ($conv->userId != Auth::user()->id) return view('errors/404');
		return ($id);
	}

	public function delete($id){
		if (!Auth::check()) return redirect('/login');
		$conversation = Conversation::where('id', $id)->firstOrFail();
		if ($conversation->userId != Auth::user()->id) return redirect('/login');
		$conversation->forceDelete();
		return redirect('/');
	}
}
