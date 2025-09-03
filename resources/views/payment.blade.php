@extends('checkout', [
  'title'  => 'Paiement'
])

@section('content')
<form id="make_order_form" action="/" method="POST" @submit="onSubmit">
  @csrf
  <div class="payment-section">
    <div>
      <div class="checkout-section-title">
        <h2>Informations de paiement</h2>
        @if (!$user)
        <span>
          <a href="{{ route('checkout.login') }}">Je dispose d'un compte</a>
        </span>
        @endif
      </div>
      @if ($user)
      <div class="checkout-information-wrapper">
        <div class="customer-information">
          <div class="left">
            <span class="label">Nom:</span>
            <span>{{ $user->getName() }}</span>
          </div>
          <div class="right">
            {{-- <span><a class="link" href="{{ route('store.checkout.customer') }}">Modifier</a></span> --}}
          </div>
        </div>
        <div class="customer-information">
          <div class="left">
            <span class="label">Email:</span>
            <span>{{ $user->getEmail() }}</span>
          </div>
          <div class="right">
          </div>
        </div>
      </div>
      @else
      <div class="checkout-information-form-wrapper">
        <div class="customer-information-form form">
          <div class="input-wrapper @error('name') has-error @enderror">
            <input type="text" name="name" placeholder="Nom complet" autocomplete="name" value="{{ old('name') }}" />
            @error('name')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>
          <div class="input-wrapper @error('email') has-error @enderror">
            <input type="text" name="email" placeholder="Email" autocomplete="email" value="{{ old('email') }}" />
            @error('email')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>
          <div class="input-wrapper @error('password') has-error @enderror">
            <input type="password" name="password" placeholder="Mot de passe" autocomplete="new-password" />
            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>
          <div class="input-wrapper @error('password_confirmation') has-error @enderror">
            <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" autocomplete="new-password" />
            @error('password_confirmation')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="note">
          <ul>
            <li>
              Veuillez vous rappeler de votre email et mot de passe, ils seront nécessaires pour accéder aux magazines.
            </li>
            <li>
              En créant un compte, vous acceptez nos <a href="#">conditions d'utilisation</a> et notre <a href="#">politique de confidentialité</a>.
            </li>
          </ul>
        </div>
      </div>
      @endif
    </div>
    <div class="payment-methods-wrapper">
      <div class="checkout-section-title">
        <h2>Mode de paiement</h2>
      </div>
      <div class="payment-methods">
        <ul>
          <li class="display">
            <div class="payment-header">
              <div class="payment-title">
                <span class="circle"></span><h3>Par carte bancaire via CMI</h3>
              </div>
              <div class="payment-image">
                <img src="/build/images/logo_cmi.png" alt="secure payment" />
              </div>
            </div>
            <div class="payment-content">
              <p>Vous serez redirigé vers la plateforme de paiement sécurisé CMI pour finaliser votre achat.</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="actions">
      <button class="button">Valider le paiement</button>
      <a href="http://the.voice:8080/">Revenir à l'accueil</a>
    </div>
  </div>
</form>
@endsection

@section('store-scripts')
@endsection
