@extends('layouts/default')

@section('pagetitle') Conversation - ChatProject @endsection

@section('navbar')
<li class="nav-item"><a href="#" class="nav-link">Accueil</a></li>
<li class="nav-item"><a href=" /contacts" class="nav-link">Contacts</a></li>
<li class="nav-item"><a href=" /account" class="nav-link">Compte</a></li>
<li class="nav-item"><a href=" /logout" class="nav-link">Déconnexion <i>({{ Auth::user()->name }})</i></a></li>
@endsection

@section('script')
<script type="text/javascript">
	$(document).ready(function() {
		$("body").tooltip({ selector: '[data-toggle=tooltip]' });
	});
</script>
@endsection

@section('content')
<div class="container">
	<div class="row">
			<h2>{{ $conv->destUser->name }}</h2>
			<i class="pull-right">{{ $conv->destUser->email }}</i>
	</div>
{{-- 		<div class="pull-right col-md-4 hidden-sm hidden-xs">
			<h4 class="pull-right">{{ $user->name }} <i>({{ $user->email }})</i></h4>
			<i class="pull-right">Token : <a href="/token/renew">{{ $user->token->token }}</a></i>		</div>
		</div> --}}


{{-- 		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Nom</th>
					<th>Dernier message</th>
					<th class="pull-right">Supprimer</th>
				</tr>
			</thead>
		@foreach ($conversations as $conversation)
		<tr>
			<td><a href="/conversation/{{ $conversation->id }}" data-toggle="tooltip" title="{{ $conversation->destUser->email }}" data-placement="right">{{ $conversation->destUser->name }}</a></td>
			<td>TODO</td>
			<td><a class="btn btn-danger btn-sm pull-right" href="/conversation/{{ $conversation->id }}/delete" role="button"><i class="fa fa-trash-o"></i></a></td>
		</tr>
		@endforeach
	</table> --}}
</div>
@endsection
