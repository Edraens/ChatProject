@extends('layouts/default')

@section('pagetitle') Conversation - ChatProject @endsection

@section('navbar')
<li class="nav-item"><a href="/" class="nav-link">Accueil</a></li>
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
		<div class="col-sm-5">
			<h2><b>{{ $conv->destUser->name }}</b> <i>({{ $conv->destUser->email }})</i> <a href="/conversation/{{ $conv->id }}" class="btn btn-lg btn-success"><i class="fa fa-lg fa-refresh"></i></a></h2>
		</div>
		<div class="col-xs-7">
			<div class="pull-right">
				{{ $messages->links() }}
			</div>
		</div>
	</div>
{{-- 		<div class="pull-right col-md-4 hidden-sm hidden-xs">
			<h4 class="pull-right">{{ $user->name }} <i>({{ $user->email }})</i></h4>
			<i class="pull-right">Token : <a href="/token/renew">{{ $user->token->token }}</a></i>		</div>
		</div> --}}
		<form class="from-inline" action="/conversation/{{ $conv->id }}" method="post" accept-charset="utf-8">
			{{ csrf_field() }}
			<div class="row">
				<div class="form-group">
					<div class="col-sm-11 col-xs-10">
						<input type="text" class="form-control" name="msg" id="msg" placeholder="Message" autocomplete="off" autofocus>
					</div>
					<div class="col-sm-1 col-xs-1">
						<button type="submit" id="submit" class="btn btn-primary"><i class="fa fa-send"></i></button>
					</div>
				</div>
			</div>
		</form>

		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="col-xs-2">Émetteur</th>
					<th class="col-xs-10">Message</th>
					
				</tr>
			</thead>
			@foreach ($messages as $message)
			<tr data-toggle="tooltip" title="{{ $message->created_at->format('d/m/Y H:i:s') }}" data-placement="top">
				<td>@if ($message->unread) <i class="fa fa-exclamation-circle"></i> @endif @if ($message->sender->id == Auth::user()->id) <i> @else <b> @endif {{ $message->sender->name }} @if ($message->sender->id == Auth::user()->id) </i> @else </b> @endif</td>
				<td>@if ($message->sender->id == Auth::user()->id) <i> @endif {{ $message->content }} @if ($message->sender->id == Auth::user()->id) </i> @endif</td>
				<td><a class="btn-xs pull-right" href="/message/{{ $message->id }}/delete" role="button"><i class="fa fa-trash-o"></i></a></td>
			</tr>
			@endforeach
		</table>

		@endsection
