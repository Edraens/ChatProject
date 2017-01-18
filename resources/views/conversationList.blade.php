@extends('layouts/default')

@section('pagetitle') ChatProject @endsection

@section('navbar')
<li class="nav-item active"><a href="#" class="nav-link">Accueil</a></li>
<li class="nav-item"><a href=" /contacts" class="nav-link">Contacts</a></li>
<li class="nav-item"><a href=" /logout" class="nav-link">Déconnexion <i>({{ Auth::user()->email }})</i></a></li>
@endsection

@section('script')
@endsection

@section('content')
<div class="container">
	<h2>{{ $user->email }}</h2>
	<i>Token : <a href="/token/renew">{{ $token->token }}</a></i>
</div>
@endsection
