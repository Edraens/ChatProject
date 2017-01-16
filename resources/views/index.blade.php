@extends('layouts/default')

@section('pagetitle') ChatProjesst @endsection

@section('navbar')
<li class="nav-item active"><a href="#" class="nav-link">Accueil</a></li>
@if (Auth::check())
<li class="nav-item"><a href=" /contacts" class="nav-link">Contacts</a></li>
<li class="nav-item"><a href=" /logout" class="nav-link">Déconnexion <i>({{ Auth::user()->email }})</i></a></li>
@else
<li class="nav-item"><a href="/login" class="nav-link">Connexion</a></li>
<li class="nav-item"><a href="/register" class="nav-link">Inscription</a></li>
@endif
@endsection

@section('script')
@endsection

@section('content')
<div class="container">
	<h1>ChatProject</h1>
</div>
@endsection
