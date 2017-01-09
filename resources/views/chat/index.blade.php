@extends('default')

@section('pagetitle') ChatProject @endsection

@section('navbar')
<li class="nav-item active"><a href="#" class="nav-link">Home</a></li>
@if (Auth::check())
<li class="nav-item"><a href=" /logout" class="nav-link">Logout <i>({{ Auth::user()->name }})</i></a></li>
@else
<li class="nav-item"><a href="/login" class="nav-link">Login</a></li>
<li class="nav-item"><a href="/register" class="nav-link">Register</a></li>
@endif
@endsection

@section('script')
@endsection

@section('content')
<div class="container">
	<h1>ChatProject</h1>
</div>
@endsection
