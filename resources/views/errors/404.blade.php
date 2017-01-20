@extends('layouts/default')

@section('pagetitle') 404 - ChatProject @endsection

@section('navbar')
<li class="nav-item"><a href="#" class="nav-link">Accueil</a></li>
@if (Auth::check())
<li class="nav-item"><a href=" /contacts" class="nav-link">Contacts</a></li>
<li class="nav-item"><a href=" /account" class="nav-link">Compte</a></li>
<li class="nav-item"><a href=" /logout" class="nav-link">Déconnexion <i>({{ Auth::user()->email }})</i></a></li>
@else
<li class="nav-item"><a href="/login" class="nav-link">Connexion</a></li>
<li class="nav-item"><a href="/register" class="nav-link">Inscription</a></li>
@endif
@endsection

@section('content')
<div class="container">
<div class="text-center">
<div class="jumbotron">
  <h1><i>Erreur 404</i></h1>
  <p class="lead hidden-xs">Impossible de trouver la conversation, l'utilisateur ou le message demandé.</p>
  <hr class="m-y-2">
  <p class="lead">
    <a class="btn btn-danger btn-lg" href="/" role="button">Retour à l'accueil</a>
  </p>
</div>
</div>
</div>
@endsection
