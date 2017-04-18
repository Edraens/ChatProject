<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Token;
use Auth;

class TokenController extends Controller
{
	// Récupération du token
    public function get(){
    	if (!Auth::check()) return redirect('/login');
    	// Récupération du token
    	$token = Token::where('userId', Auth::user()->id)->first();
    	return ($token->token);
    }
    // Regénération du token
    public function renew(){
    	if (!Auth::check()) return redirect('/login');
    	// Génération du token et remplacement s'il existe déjà
    	$genToken = str_random(15);
		$exists = Token::where('token', $genToken)->first();
		while (!is_null($exists)) {
			$genToken = str_random(15);
			$exists = Token::where('token', $genToken)->first();
		}

		$exists = Token::where('token', $genToken)->first();

		$alreadyGenerated = Token::where('userId', Auth::user()->id)->first();
		if (!is_null($alreadyGenerated)) {
			$alreadyGenerated->forceDelete();
		}
		// Création du token
		Token::Create([
			'userId' => Auth::user()->id,
			'token' => $genToken,
			]);
		return redirect('/');
    }
}
