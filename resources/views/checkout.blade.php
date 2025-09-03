@extends('html', [
  'title' => $title,
])

@section('app')
<div class="site-wrapper" id="app">
  <section class="site-content">
    <div class="page-wrapper no-padding checkout-page with-spinner">
      <div class="container">
        <div class="checkout-content">
          <main class="checkout-main-content">
            <div class="site-branding">
              <div class="brand-logo">
                <a href="">
                  <img src="/build/images/the-voice-logo.png" alt="logo" />
                </a>
              </div>
            </div>
            <div class="breadcrumbs">
              <ol>
                <li>
                  <a href="http://the.voice:8080">Accueil</a>
                </li>
                <li>
                  <span class="sep">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                      <path d="M439.1 297.4C451.6 309.9 451.6 330.2 439.1 342.7L279.1 502.7C266.6 515.2 246.3 515.2 233.8 502.7C221.3 490.2 221.3 469.9 233.8 457.4L371.2 320L233.9 182.6C221.4 170.1 221.4 149.8 233.9 137.3C246.4 124.8 266.7 124.8 279.2 137.3L439.2 297.3z"/>
                    </svg>
                  </span>
                </li>
                <li>
                  <span>Paiement</span>
                </li>
              </ol>
            </div>
            @yield('content')
            <div class="checkout-footer">
              <div class="footer-image">
                <img src="/build/images/secure-payment.webp" alt="secure payment" />
              </div>
            </div>
          </main>
          <div class="cart-summary-wrapper">
            <div class="cart cart-summary">
              <div class="checkout-section-title">
                <h2>Récapitulatif du panier</h2>
              </div>
              <div class="items-list">
                <ul>
                  @foreach ($cart->getDisplayItems($price) as $displayItem)
                  <li>
                    <div class="item">
                      <div class="item-details with-image">
                        <div class="item-media">
                          <div class="product-image">
                            <img src="{{ $displayItem['thumbnail'] }}" alt="">
                          </div>
                        </div>
                        <div class="item-info">
                          <div class="item-title">{{ $displayItem['title'] }}</div>
                          <div class="item-options">
                            <span>{{ $displayItem['price'] }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="item-total">{{ $displayItem['price'] }}</div>
                    </div>
                  </li>
                  @endforeach
                </ul>
              </div>
              <div class="cart-total-wrapper">
                <div class="cart-subtotal">
                  <span class="total-text">Sous-total</span>
                  <span class="total-value">{{ $price }} MAD</span>
                </div>
                <div class="cart-total">
                  <div class="total-left">
                    <span class="total-text">Total</span>
                    <div class="total-taxes">Taxes incluses</div>
                  </div>
                  <span class="total-value">{{ $price }} MAD</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('styles')
@vite('resources/scss/checkout.scss')
@endsection

@section('scripts')
@endsection
