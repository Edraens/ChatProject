@extends('layouts/default')

@section('pagetitle') ChatProject @endsection

@section('navbar')
<li class="nav-item active"><a href="#" class="nav-link">Accueil</a></li>
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
	@if ($errors->has('email'))
	<div class="alert alert-danger" role="alert">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
		<strong>Impossible d'ouvrir la conversation :</strong> <i>Indiquez une adresse email correcte.</i>
	</div>
	@endif

	<div class="row">
		<div class="col-md-3"><h2>Conversations</h2></div>
		<div class="pull-right col-md-4 hidden-sm hidden-xs">
			<span class="pull-right">{{ $user->name }} <i>({{ $user->email }})</i></span><br />
			<i class="pull-right">Token : {{ $user->token->token }} <a href="/token/renew">(Renouveler)</a></i>		
		</div>
	</div>
	<br />

	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th><i class="fa fa-exclamation-circle"></i></th>
				<th>Utilisateur</th>
				<th>Date</th>
				<th>Dernier message</th>
				<th class="pull-right"><i class="fa fa-trash-o"></i></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($conversations as $conversation)
			<tr>
				<td> @if ($conversation->hasUnread)<i class="fa fa-exclamation-circle fa-2x"></i> @endif </td>
				<td class="col-xs-2 table-warning"><a href="/conversation/{{ $conversation->id }}" data-toggle="tooltip" title="{{ $conversation->destUser->email }}" data-placement="right">{{ $conversation->destUser->name }}</a></td>
				@if ($lastMessage[$conversation->id] != "")
				<td class="col-xs-2"><span class="hidden-xs"><a href="/conversation/{{ $conversation->id }}"><small>{{ $lastMessage[$conversation->id]->created_at->format('d/m/Y') }}</small></span> <b>{{ $lastMessage[$conversation->id]->created_at->format('H:i:s') }}</b></a></td>
				<td class="col-xs-8">@if ($lastMessage[$conversation->id]->sender->id == Auth::user()->id) <i> @else <b> @endif @if (strlen($lastMessage[$conversation->id]->content) <= 60) {{ $lastMessage[$conversation->id]->content }} @else {{ mb_substr($lastMessage[$conversation->id]->content,0,60,'UTF-8') }}... @endif  @if ($lastMessage[$conversation->id]->sender->id == Auth::user()->id) </i> @else </b> @endif</td>
				@else
				<td><a href="/conversation/{{ $conversation->id }}"><i>Aucun message</i></a></td>
				<td><i>Aucun message</i></td>
				@endif
				<td class="pull-right"><a class="btn btn-danger btn-sm pull-right" href="/conversation/{{ $conversation->id }}/delete" role="button"><i class="fa fa-trash-o"></i></a></td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<div class="row pull-right">
		<div class="col-xs-3">
			<button type="submit" class="btn btn-lg btn-primary" data-toggle="modal" data-target="#newConv">Nouvelle conversation</button>
		</div>
	</div>

	<div class="modal fade" id="newConv" tabindex="-1" role="dialog" aria-labelledby="preview" aria-hidden="true">
		<form class="form" action="/" method="post" accept-charset="utf-8">
			{{ csrf_field() }}
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" id="preview">Nouvelle conversation</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="email">Adresse e-mail du correspondant</label>
							<input type="text" class="form-control" name="email" id="email" placeholder="john@doe.com" maxlength="50">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
						<button type="submit" class="btn btn-primary">Ouvrir</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection
