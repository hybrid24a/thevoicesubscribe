@extends('html', [
  'title' => $title,
  'bodyClass' => 'rtl',
])

@section('app')
<div class="site-wrapper" id="app">
  <section class="site-content">
    <div class="page-wrapper no-padding checkout-page with-spinner">
      <div class="container">
        <div class="checkout-mobile-header">
          @include('partials.header')
        </div>
      </div>
      <div class="container">
        <div class="checkout-content">
          <main class="checkout-main-content">
            <div class="checkout-desktop-header">
              @include('partials.header')
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
                <h2>مراجعة الطلب</h2>
              </div>
              <div class="items-list">
                <ul>
                  @foreach ($cart->getDisplayItems() as $displayItem)
                  <li>
                    <div class="item">
                      <div class="item-details with-image">
                        <div class="item-media">
                          <img src="{{ $displayItem['thumbnail'] }}" alt="">
                        </div>
                        <div class="item-info">
                          @if (!empty($displayItem['subtitle']))
                            <div class="item-subtitle">{{ $displayItem['subtitle'] }}</div>
                          @endif
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
                <div class="cart-total">
                  <div class="total-left">
                    <span class="total-text">المجموع</span>
                    <div class="total-taxes">الضرائب مشمولة</div>
                  </div>
                  <span class="total-value">{{ $cart->getFormattedTotal() }} Dh</span>
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
