<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \Input;
use App\User;
use App\Token;

class UserController extends Controller
{
	public function index(){
		if (!Auth::check()) return redirect('/login');
		$user = User::where('id', Auth::user()->id)->first();

		return view('account', ['user' => $user]);
	}

	public function edit(Request $request){
		if (!Auth::check()) return redirect('/login');
		$this->validate($request, [
			'name' => 'max:30|required',
			]);
		$user = User::where('id', Auth::user()->id)->first();
		$user->name = Input::get('name');
		$user->save();
		return redirect('/account');
	}

	public function APIAuth($token){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$userId = $token->user->id;

		return ($token->user);
	}
}
