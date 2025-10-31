<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title>{{ $title }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  @vite('resources/scss/admin.scss')
  @yield('head')
</head>
<body>
  <header class="main-header">
    <div class="side-menu">
      <div class="brand-logo">
        <a href="/">
          <img src="{{ asset('/build/images/logo-dark.svg') }}" alt="Logo" />
        </a>
      </div>
      <nav class="main-menu">
        <ul>
          <li class="{{ request()->routeIs('admin.orders.list') ? 'active' : '' }}">
            <a href="{{ route('admin.orders.list') }}">
              <span class="icon"><i class="fas fa-cog"></i></span>
              <span class="text">Commandes</span>
            </a>
          </li>
          <li class="{{ request()->routeIs('admin.params') ? 'active' : '' }}">
            <a href="{{ route('admin.params') }}">
              <span class="icon"><i class="fas fa-cog"></i></span>
              <span class="text">Paramètres</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </header>
  <div class="page-wrapper">
    <div class="page-header">
      <div class="container">
        <div class="page-header-wrapper">
          <div class="title">
            <h1>{!! $title !!}</h1>
          </div>
          <div class="user-box">
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
            <a class="menu-button" href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <span class="text">{{ $admin->getName() }}</span>
              <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                  <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                </svg>
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
    @if (session('success'))
      <div class="notice success">
        <div class="icon">
          <i class="fas fa-check"></i>
        </div>
        <div class="text">
          {{ session('success') }}
        </div>
      </div>
    @endif
    @if (session('error'))
      <div class="notice error">
        <div class="icon">
          <i class="fas fa-times"></i>
        </div>
        <div class="text">
          {{ session('error') }}
        </div>
      </div>
    @endif
    <div class="page-content">
      <div class="container">
        @yield('content')
      </div>
    </div>
  </div>
  @yield('footer')
</body>
</html>
