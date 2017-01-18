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
		$user = User::where('id', Auth::user()->id)->first();
		return view('conversationList', ['token' => $token, 'user' => $user]);
	}
}
