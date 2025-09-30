@extends('admin.auth.layout', [
  'title'  => 'Login',
])

@section('content')
<div class="auth-form-wrapper">
  <div class="auth-form">
    <form method="POST" action="{{ route('admin.login') }}">
      @csrf
      <div class="auth-form-title">
        <h1 class="auth-form-title">S'identifier</h1>
      </div>
      @if ($errors->any())
        <div class="notices">
          @foreach ($errors->all() as $error)
          <div class="notice error">
            {{ $error }}
          </div>
          @endforeach
        </div>
      @endif
      <div class="form-inputs">
        <div class="input-wrapper">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        </div>
        <div class="input-wrapper">
          <label for="password">Mot de passe</label>
          <input id="password" type="password" name="password" required>
        </div>
        <div class="action-wrapper">
          <button type="submit" class="button">S'identifier</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
