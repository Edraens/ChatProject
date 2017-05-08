@extends('layouts/default')

@section('pagetitle') ChatProject @endsection

@section('navbar')
<li class="nav-item active"><a href="#" class="nav-link">Accueil</a></li>
<li class="nav-item"><a href="/login" class="nav-link">Connexion</a></li>
<li class="nav-item"><a href="/register" class="nav-link">Inscription</a></li>
@endsection

@section('script')
@endsection

@section('content')
<div class="jumbotron">
  <h1 class="display-3">ChatProject</h1>
  <p class="lead">Discutez sans limite, gratuitement, avec une adresse e-mail.</p>
  <hr class="my-4">
  <p>Inscrivez vous dès maintenant :</p>
  <p class="lead">
    <a class="btn btn-primary btn-lg" href="/register" role="button">Inscription</a>
  </p>
</div>
@endsection
