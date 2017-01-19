@extends('layouts/default')

@section('pagetitle') Compte - ChatProject @endsection

@section('navbar')
<li class="nav-item"><a href="/" class="nav-link">Accueil</a></li>
<li class="nav-item"><a href=" /contacts" class="nav-link">Contacts</a></li>
<li class="nav-item active"><a href=" /account" class="nav-link">Compte</a></li>
<li class="nav-item"><a href=" /logout" class="nav-link">Déconnexion <i>({{ Auth::user()->name }})</i></a></li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <h2 class="text-center display-4">Compte <i>({{ $user->email }})</i></h2>
    </div><br />
    <form class="form-horizontal" role="form" method="POST" action="/account">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-4 control-label">Nom</label>

            <div class="col-md-3">
                <input id="name" type="text" class="form-control" name="name" maxlength="30" value="{{ $user->name }}" >
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-btn fa-check-circle"></i> Envoyer
                </button>
            </div>
        </div>
    </form>
</div>
@endsection