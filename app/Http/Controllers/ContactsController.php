<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use Auth;
use App\User;
use App\Token;
use \Input;
use Validator;

class ContactsController extends Controller
{
	public function showall(){
		if (!Auth::check()) return redirect('/login');
		$contacts = Contact::where('userId', Auth::user()->id)->get();
		// return response()->json($contacts);
		return view('contacts', ['contacts' => $contacts]);
	}

	public function add(Request $request){
		if (!Auth::check()) return redirect('/login');
		$this->validate($request, [
			'name' => 'max:40|required',
			'email' => 'max:40|required|email',
			]);
		$userId = Auth::user()->id;
		$name = Input::get('name');
		$email = Input::get('email');	

		$checkExists = Contact::where('email', $email)->first();
		if (!is_null($checkExists)) {
			return redirect('/contacts');
		}

		Contact::create([
			'userId' => $userId,
			'name' => $name,
			'email' => $email,
			]);
		return redirect('/contacts');
	}

	public function delete($id){
		if (!Auth::check()) return redirect('/login');
		$contact = Contact::where('id', $id)->firstOrFail();
		if ($contact->userId != Auth::user()->id) return redirect('/login');
		$contact->forceDelete();
		return redirect('/contacts');
	}

	public function APIshowall($token){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$user = $token->user;
		$contacts = Contact::where('userId', $user->id)->get();
		// return response()->json($contacts);
		return($contacts);
	}

	public function APIadd($token, Request $request){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$user = $token->user;

		$validator = Validator::make($request->all(), [
			'name' => 'max:40|required',
			'email' => 'max:40|required|email',
			]);
		
		if ($validator->fails()) {
			return response()->json([
				'done' => 'false',
				]);

		}

		$userId = $user->id;
		// dd(Input::get('name'));
		$name = Input::get('name');
		$email = Input::get('email');	

		$checkExists = Contact::where('email', $email)->first();
		if (!is_null($checkExists)) {
			return response()->json([
				'done' => 'false',
				]);
		}

		Contact::create([
			'userId' => $userId,
			'name' => $name,
			'email' => $email,
			]);
		return response()->json([
			'done' => 'true',
			]);
	}

	public function APIdelete($token, $id){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$user = $token->user;
		$contact = Contact::where('id', $id)->first();
		if (is_null($contact)){
			return response()->json([
				'done' => 'false',
				]);
		}

		if ($contact->userId != $user->id) return response('User not found', 404);
		$contact->forceDelete();
		return response()->json([
			'done' => 'true',
			]);
	}

}
