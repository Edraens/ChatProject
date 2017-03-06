<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Token;
use Auth;
use App\User;
use App\Conversation;
use App\Message;
use \Input;
use Validator;

class ChatController extends Controller
{
	public function index(){
		if (!Auth::check()) return view('index');
		$user = User::where('id', Auth::user()->id)->first();
		$token = Token::where('userId', Auth::user()->id)->first();
		if (is_null($token)) return redirect ('/token/renew');

		$conversations = Conversation::where('userId', Auth::user()->id)->orderBy('lastActivity', 'desc')->get();
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

		$conv->lastActivity = time();
		$conv->save();

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
			$correspConv->lastActivity = time();
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

	public function APIlist($token){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$user = $token->user;

		$conversations = Conversation::where('userId', $user->id)->orderBy('lastActivity', 'desc')->get();
		$lastMessage = "";
		foreach ($conversations as $conversation) {
			$getConvInfo = $conversation->destUser
			;
			$foundMsg = Message::where('conversationId', $conversation->id)->orderBy('created_at', 'desc')->first();
			if (is_null($foundMsg)) {
				$lastMessage[$conversation->id] = "";
			}
			else {
				$lastMessage[$conversation->id]["date"] = $foundMsg->created_at->format('d-m-Y H:i:s');

				if (strlen($foundMsg->content) <= 50) { 
					$lastMessage[$conversation->id]["message"] = $foundMsg->content;
				}
				else { 
					$short = mb_substr($foundMsg->content,0,50,'UTF-8')."...";
					$lastMessage[$conversation->id]["message"] = $short;
				}
				if ($foundMsg->fromUser == $user->id) { 
					$lastMessage[$conversation->id]["sendByMe"] = true;
				}
				else {
					$lastMessage[$conversation->id]["sendByMe"] = false;
				}
			}
		}

		return response()->json([
			'done' => 'true',
			'conversations' => $conversations,
			'lastMessage' => $lastMessage,
			]);
	}

	public function APIopenConversation($token, $email){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$user = $token->user;

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return response()->json([
				'done' => 'false',
				]);
		}
		$findDest = User::where('email', $email)->first();
		if (is_null($findDest)) return response()->json(['done' => 'false']);
		if ($findDest->id == $user->id) return response()->json(['done' => 'false']);

		$findConversation = Conversation::where([['userId', '=', $user->id], ['destId', '=', $findDest->id]])->first();
		if (is_null($findConversation)) {
			Conversation::create([
				'userId' => $user->id,
				'destId' => $findDest->id,
				'hasUnread' => false,
				'lastActivity' => time(),
				]);
			$findConversation = Conversation::where([['userId', '=', $user->id], ['destId', '=', $findDest->id]])->first();
		}

		return response()->json([
			'done' => 'true',
			'convId' => $findConversation->id,
			]);
	}

	public function APIshow($token, $id){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$user = $token->user;

		$conv = Conversation::where('id', $id)->first();
		if (is_null($conv)) {
			return response()->json([
				'done' => 'false',
				]);
		} 
		if ($conv->userId != $user->id) return response()->json(['done' => 'false']);

		$messages = Message::where('conversationId', $conv->id)->orderBy('created_at', 'DESC')->limit(50)->get();
		$unreadMessages = Message::where([['conversationId', '=', $conv->id], ['unread', '=', true]])->get();
		foreach ($unreadMessages as $unreadMessage) {
			$unreadMessage->unread = false;
			$unreadMessage->save();
		}
		$conv->hasUnread = false;
		$conv->save();

		$convInfoUser = $conv->destUser->name;
		$convInfomail = $conv->destUser->email;

		foreach ($messages as $message) {
			if ($message->fromUser == $user->id) $msgFromMe[$message->id] = true;
			else $msgFromMe[$message->id] = false;
		}

		return response()->json([
			'done' => 'true',
			'conv' => $conv,
			'messages' => $messages,
			'destUsername' => $conv->destUser->name,
			'msgFromMe' => $msgFromMe,
			]);
	}

	public function APIsend($token, $id, Request $request){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$user = $token->user;
		$validator = Validator::make($request->all(), [
			'msg' => 'required',
			]);
		
		if ($validator->fails()) {
			return response()->json([
				'done' => 'false',
				]);

		}

		$conv = Conversation::where('id', $id)->first();
		if (is_null($conv)) {
			return response()->json([
				'done' => 'false',
				]);
		} 
		if ($conv->userId != $user->id) return response()->json(['done' => 'false']);

		$conv->lastActivity = time();
		$conv->save();

		$correspConv = Conversation::where([['userId', '=', $conv->destUser->id], ['destId', '=', $user->id]])->first();
		if (is_null($correspConv)) {
			Conversation::create([
				'userId' => $conv->destUser->id,
				'destId' => $user->id,
				'hasUnread' => true,
				'lastActivity' => time(),
				]);
			$correspConv = Conversation::where([['userId', '=', $conv->destUser->id], ['destId', '=', $user->id]])->first();
		}
		else {
			$correspConv->hasUnread = true;
			$correspConv->lastActivity = time();
			$correspConv->save();
		}

		Message::create([
			'fromUser' => $user->id,
			'belongsTo' => $user->id,
			'toUser' => $conv->destUser->id,
			'content' => Input::get('msg'),
			'conversationId' => $conv->id,
			'unread' => false,
			]);

		Message::create([
			'fromUser' => $user->id,
			'belongsTo' => $conv->destUser->id,
			'toUser' => $conv->destUser->id,
			'content' => Input::get('msg'),
			'conversationId' => $correspConv->id,
			'unread' => true,
			]);

		return response()->json(['done' => 'true']);
	}

	public function APIdeleteConversation($token, $id){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$user = $token->user;

		$conv = Conversation::where('id', $id)->first();
		if (is_null($conv)) {
			return response()->json([
				'done' => 'false',
				]);
		}

		if ($conv->userId != $user->id) return response()->json(['done' => 'false']);
		$messages = Message::where('conversationId', $id);
		$messages->forceDelete();
		$conv->forceDelete();
		return response()->json(['done' => 'true']);
	}

	public function APIdeleteMessage($token, $id){
		$token = Token::where('token', $token)->first();
		if (is_null($token)) {
			return response('User not found', 404);
		}
		$user = $token->user;

		$message = Message::where('id', $id)->first();
		if (is_null($message)) {
			return response()->json([
				'done' => 'false',
				]);
		}

		$convId = $message->conversation->id;
		if ($message->belongsTo != $user->id) return response()->json(['done' => 'false']);
		$message->forceDelete();
		return response()->json(['done' => 'true']);
	}

}
