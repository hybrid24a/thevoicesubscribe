<!doctype html>
<html lang="fr">
  <head>
    <link rel="icon" href="https://thevoice.ma/wp-content/uploads/2023/07/cropped-favicon-32x32.png" sizes="32x32" />
    <link rel="icon" href="https://thevoice.ma/wp-content/uploads/2023/07/cropped-favicon-192x192.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://thevoice.ma/wp-content/uploads/2023/07/cropped-favicon-180x180.png" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ucfirst($title) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('styles')
  </head>
  <body class="rtl">
    @yield('app')
    @yield('scripts')
  </body>
</html>
