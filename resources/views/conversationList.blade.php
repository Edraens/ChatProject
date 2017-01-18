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
	<i>Token : <a href="/token/renew">{{ $token }}</a></i>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>Email</th>
				<th>Dernier message</th>
				<th class="pull-right">Supprimer</th>
			</tr>
		</thead>
	</tbody>
	@foreach ($conversations as $conversation)
	<tr>
		<td><a href="/conversation/{{ $conversation->id }}">{{ $nameToConv[$conversation->id] }}</a></td>
		<td>TODO</a></td>
		<td><a class="btn btn-danger btn-sm pull-right" href="/conversation/{{ $conversation->id }}/delete" role="button"><i class="fa fa-trash-o"></i></a></td>
	</tr>
	@endforeach
</tbody>
</table>
</div>
@endsection
