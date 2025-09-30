@extends('html', [
  'title' => $title,
])

@section('app')
<div class="site-wrapper">
  <div class="auth-wrapper">
    @yield('content')
  </div>
</div>
@endsection

@section('styles')
@vite('resources/scss/admin.scss')
@endsection

@section('scripts')
@endsection
