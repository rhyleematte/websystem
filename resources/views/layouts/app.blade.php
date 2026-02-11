<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'AskDocPH')</title>

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  {{-- add inside <head> after your auth.css --}}
  @stack('styles')



</head>
<body>

<header class="topbar">
  <div class="brand">
    <img src="{{ asset('assets/img/AskDocPH.png') }}" class="logo">
  </div>

  <nav class="nav">
    <a href="{{ route('about') }}" class="nav-link">About</a>
    <a href="{{ route('signup') }}" class="btn signup-btn">Sign Up</a>
  </nav>
</header>

@yield('content')

<!-- JS -->
<script src="{{ asset('assets/js/auth.js') }}" defer></script>
@stack('scripts')
</body>
</html>
