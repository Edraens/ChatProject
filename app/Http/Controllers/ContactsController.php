<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use Auth;
use \Input;

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

}
