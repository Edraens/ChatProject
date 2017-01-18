<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Token;
use Auth;
use App\User;

class ChatController extends Controller
{
	public function index(){
		if (!Auth::check()) return view('index');

		$token = Token::where('userId', Auth::user()->id)->first();
		if (is_null($token)) {
			return redirect('/token/renew');
		}
		$user = User::where('id', Auth::user()->id)->first();
		return view('conversationList', ['token' => $token->token, 'user' => $user]);
	}

	public function openConversation($email){
		if (!Auth::check()) return redirect('/login');
		$user = User::where('email', $email)->firstOrFail();
		if ($user->id == Auth::user()->id) return view('errors/404');
		return ($user->email);
	}
}
