@extends('layouts/default')

@section('pagetitle') ChatProject @endsection

@section('navbar')
<li class="nav-item"><a href="/" class="nav-link">Accueil</a></li>
<li class="nav-item active"><a href="#" class="nav-link">Contacts</a></li>
<li class="nav-item"><a href=" /account" class="nav-link">Compte</a></li>
<li class="nav-item"><a href="/logout" class="nav-link">Déconnexion <i>({{ Auth::user()->name }})</i></a></li>
@endsection

@section('script')
@endsection

@section('content')
<div class="container">

	<h2 class="text-center display-4">Contacts</h2>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Nom</th>
				<th>Email</th>
				<th></th>
			</tr>
		</thead>
	</tbody>
	@foreach ($contacts as $contact)
	<tr>
		<th scope="row"><i>{{ $loop->iteration }}</i></th>
		<td>{{ $contact->name }}</td>
		<td><a href="/open/{{ $contact->email }}">{{ $contact->email }}</a></td>
		<td><a class="btn btn-danger btn-sm pull-right" href="/contacts/{{ $contact->id }}/delete" role="button"><i class="fa fa-trash-o"></i></a></td>
	</tr>
	@endforeach
</tbody>
</table>
<div class="row"></div>
<h4><i>Ajouter un contact</i></h4>
<form class="from-inline" action="/contacts" method="post" accept-charset="utf-8">
	{{ csrf_field() }}
	<div class="row">
		<div class="form-group">
			<div class="col-sm-4 col-xs-5 @if ($errors->has('name')) has-error @endif">
				<input type="text" class="form-control" name="name" id="name" placeholder="Nom" autocomplete="off" autofocus>
			</div>
			<div class="col-sm-4 col-xs-5 @if ($errors->has('email')) has-error @endif">
				<input type="email" class="form-control" name="email" id="email" placeholder="Adresse email" autocomplete="off" autofocus>
			</div>
			<div class="col-sm-1 col-xs-1">
				<button type="submit" id="submit" class="btn @if (count($errors) > 0) btn-danger @else btn-outline-success @endif"><i class="fa fa-check"></i></button>
			</div>
		</div>
	</div>
</form>

{{-- <form action="/contacts" method="post" accept-charset="utf-8">
	{{ csrf_field() }}
	<div class="row">
		<div class="form-group col-xs-2 @if ($errors->has('name')) has-error @endif" id="name">
			<input type="text" class="form-control" name="name" id="name" placeholder="Nom" value="{{ old('name') }}" maxlength="40">
		</div>
		<div class="form-group col-xs-2 @if ($errors->has('email')) has-error @endif" id="email">
			<input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') }}" maxlength="40">
		</div>
		<div class="form-group col-xs-4">
			<button type="submit" id="submit" class="btn @if (count($errors) > 0) btn-danger @else btn-outline-success @endif" @if (!Auth::check()) data-toggle="tooltip" data-placement="right" title="Registered users have access to other privacy tools and can bypass captchas" @endif>Envoi</button>
		</div>
	</div>
</div>
</form> --}}
@endsection
