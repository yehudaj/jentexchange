<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JentExchange')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <style>
      body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Helvetica Neue',Arial;margin:0;background:#f7fafc;color:#111}
      .container{max-width:1100px;margin:2rem auto;padding:1rem}
      header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
      nav a{margin-left:0.75rem;color:#0366d6;text-decoration:none}
      .card{background:white;padding:1.25rem;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06)}
    </style>
  </head>
  <body>
    <div class="container">
      <header>
        <div><a href="{{ url('/') }}"><strong>JentExchange</strong></a></div>
        <nav>
          @auth
            <span>Hi, {{ Auth::user()->name }}</span>
            {{-- Always show links to entertainer and customer flows; the convenience routes will redirect to create or edit appropriately. --}}
            <a href="{{ url('/me/entertainer') }}">Entertainer</a>
            <a href="{{ url('/me/customer') }}">Customer</a>
            @if(Auth::user()->isAdmin())
              <a href="{{ url('/admin') }}">Admin</a>
            @endif
            <a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
          @else
            <a href="{{ url('/signup') }}">Sign Up</a>
            <a href="{{ url('/login') }}">Sign In</a>
            <a href="{{ url('/oauth/redirect') }}">Sign In with Google</a>
            @if(auth()->check() && auth()->user()->isAdmin())
              <a href="{{ url('/admin') }}">Admin Portal</a>
            @endif
          @endauth
        </nav>
      </header>

      <main>
        @yield('content')
      </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pica/8.0.0/pica.min.js"></script>
      @stack('scripts')
    </div>
    </body>
</html>
