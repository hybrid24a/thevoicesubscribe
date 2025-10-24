<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simulating CMI</title>
  <style>
    body {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
    }
    .message {
      font-size: 24px;
      margin-bottom: 30px;
      color: #333;
    }
    .countdown {
      font-size: 72px;
      font-weight: bold;
      color: #ff6b35;
      animation: pulse 1s ease-in-out infinite;
    }
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }
  </style>
</head>
<body>
  @php
    $okRoute = route("checkout.pay.ok", ['number' => $order->getNumber()]);
  @endphp
  <div class="message">Simulating CMI</div>
  <div class="countdown" id="countdown">3</div>

  <script>
    let count = 3;
    const countdownElement = document.getElementById('countdown');

    const timer = setInterval(() => {
      count--;
      if (count > 0) {
        countdownElement.textContent = count;
      } else {
        countdownElement.textContent = '0';
        setTimeout(() => {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = "{{ $okRoute }}";

          const token = document.createElement('input');
          token.type = 'hidden';
          token.name = '_token';
          token.value = '{{ csrf_token() }}';
          form.appendChild(token);

          document.body.appendChild(form);
          form.submit();
        }, 1000);
        clearInterval(timer);
      }
    }, 1000);
  </script>
</body>
</html>
