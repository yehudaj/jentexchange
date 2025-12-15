@extends('layouts.app')

@section('title','Sign Up')

@section('content')
  <div class="card" style="max-width:520px;margin:0 auto;padding:24px;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.03);">
    <h1 style="margin-top:0">Create an account</h1>
    <p style="color:#374151">We use Google sign-in only. Choose how you want to sign up:</p>

    <div style="display:flex;flex-direction:column;gap:12px;margin-top:16px">
      <a href="{{ url('/oauth/redirect?intent=customer') }}" style="display:block;padding:12px 16px;background:#111;color:#fff;border-radius:6px;text-align:center;text-decoration:none">Continue with Google (Customer)</a>
      <a href="{{ url('/oauth/redirect?intent=entertainer') }}" style="display:block;padding:12px 16px;background:#0ea5e9;color:#03203c;border-radius:6px;text-align:center;text-decoration:none">Continue with Google (Entertainer)</a>
      <a href="{{ url('/oauth/redirect') }}" style="display:block;padding:10px 14px;border:1px solid #d1d5db;border-radius:6px;text-align:center;text-decoration:none;color:#111">Continue with Google (Generic)</a>
    </div>

    <p style="margin-top:18px;color:#6b7280;font-size:13px">By continuing you agree to our terms. No password is stored locally.</p>
  </div>
@endsection
