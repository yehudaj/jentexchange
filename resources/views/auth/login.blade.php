@extends('layouts.app')

@section('title','Sign In')

@section('content')
  <div class="card" style="max-width:420px;margin:0 auto;padding:24px;border:1px solid #e5e7eb;border-radius:8px;">
    <h1 style="margin-top:0">Sign in</h1>
    <p style="color:#374151">Sign in using Google:</p>
    <div style="margin-top:14px">
      <a href="{{ url('/oauth/redirect') }}" style="display:block;padding:12px 16px;background:#1f2937;color:#fff;border-radius:6px;text-align:center;text-decoration:none">Sign in with Google</a>
    </div>
  </div>
@endsection
