<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title','Test')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="{{ asset('js/nextbutton.js') }}?v={{ time() }}" defer></script>
</head>

<body>

  @include('partials.header')



  <main>
    @yield('content')
  </main>

  @include('partials.footer')
</body>
</html>
