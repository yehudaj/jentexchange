@extends('layouts.app')

@section('title','Hello')

@section('content')
  <div class="card">
    <h1>Hello, JentExchange!</h1>
    <p>This is a minimal Hello World page.</p>
    <p><a href="/signup">Sign up</a> or <a href="/login">Log in</a></p>
    <p><a href="/oauth/redirect">Sign in with Google</a></p>
  </div>
@endsection
