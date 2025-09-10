@extends('checkout', [
  'title'  => 'الدفع'
])

@section('content')
<form id="make_order_form" action="/" method="POST">
  @csrf
  <div class="payment-section">
    <div>
      <div class="checkout-section-title">
        <h2>معلومات الدفع</h2>
        @if (!$user)
        <span>
          <span>هل لديك حساب؟ <a href="{{ route('checkout.login') }}">تسجيل الدخول</a></span>
        </span>
        @endif
      </div>
      @if ($user)
      <div class="checkout-information-wrapper">
        <div class="customer-information">
          <div class="start">
            <span class="label">الاسم:</span>
            <span>{{ $user->getName() }}</span>
          </div>
          <div class="right">
          </div>
        </div>
        <div class="customer-information">
          <div class="start">
            <span class="label">البريد الإلكتروني:</span>
            <span>{{ $user->getEmail() }}</span>
          </div>
          <div class="right">
          </div>
        </div>
      </div>
      @else
      <div class="checkout-information-form-wrapper">
        <div class="customer-information-form form">
          <div class="radio-wrapper inline">
            <label class="label" for="individual-type">
              <input type="radio" id="individual-type" name="type" value="individual" {{ old('type', 'individual') === 'individual' ? 'checked' : '' }}>
              <span class="text">فرد</span>
            </label>
            <label class="label" for="company-type">
              <input type="radio" id="company-type" name="type" value="company" {{ old('type') === 'company' ? 'checked' : '' }}>
              <span class="text">شركة</span>
            </label>
          </div>
          <div id="name_field" class="input-wrapper @error('name') has-error @enderror">
            <input
              type="text"
              name="name"
              placeholder="الاسم الكامل"
              autocomplete="name"
              value="{{ old('name') }}"
              data-company-label="اسم الشركة"
              data-individual-label="الاسم الكامل"
            />
            @error('name')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>
          <div id="ice_field" class="input-wrapper">
            <input type="text" name="ice" placeholder="التعريف الموحد (ICE)" autocomplete="off" value="{{ old('ice') }}" />
            @error('ice')
              <div class="error">{{ $message }}</div>
            @enderror
          </div>
          <div class="input-wrapper @error('email') has-error @enderror">
            <input type="text" name="email" placeholder="البريد الإلكتروني" autocomplete="email" value="{{ old('email') }}" />
            @error('email')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>
          <div class="input-wrapper @error('password') has-error @enderror">
            <input type="password" name="password" placeholder="كلمة المرور" autocomplete="new-password" />
            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>
          <div class="input-wrapper @error('password_confirmation') has-error @enderror">
            <input type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور" autocomplete="new-password" />
            @error('password_confirmation')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="note">
          <ul>
            <li>
                يرجى تذكر بريدك الإلكتروني وكلمة المرور، فستحتاجهما للوصول إلى المجلات.
            </li>
            <li>
                بإنشائك حساب، أنت توافق على <a href="#">شروط الاستخدام</a> و <a href="#">سياسة الخصوصية</a> الخاصة بنا.
            </li>
          </ul>
        </div>
      </div>
      @endif
    </div>
    <div class="payment-methods-wrapper">
      <div class="checkout-section-title">
        <h2>طريقة الدفع</h2>
      </div>
      <div class="payment-methods">
        <ul>
          <li class="display">
            <div class="payment-header">
              <div class="payment-title">
                <span class="circle"></span><h3>عن طريق البطاقة البنكية عبر CMI</h3>
              </div>
              <div class="payment-image">
                <img src="/build/images/logo_cmi.png" alt="secure payment" />
              </div>
            </div>
            <div class="payment-content">
                <p>سيتم تحويلك إلى منصة الدفع الآمن CMI لإتمام عملية الشراء.</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="actions">
      <button class="button">تأكيد الدفع</button>
      <a href="{{ config('app.site_url') }}">العودة إلى الصفحة الرئيسية</a>
    </div>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const individualRadio = document.getElementById('individual-type');
  const companyRadio = document.getElementById('company-type');
  const iceField = document.getElementById('ice_field');
  const nameField = document.getElementById('name_field');
  const nameInput = nameField.querySelector('input[name="name"]');

  function toggleFields() {
    if (individualRadio.checked) {
      iceField.style.display = 'none';
      nameInput.placeholder = nameInput.getAttribute('data-individual-label');
    } else {
      console.log('Company selected');
      iceField.style.display = 'block';
      nameInput.placeholder = nameInput.getAttribute('data-company-label');
    }
  }

  individualRadio.addEventListener('change', toggleFields);
  companyRadio.addEventListener('change', toggleFields);

  // Initial state
  toggleFields();
});
</script>
@endsection
