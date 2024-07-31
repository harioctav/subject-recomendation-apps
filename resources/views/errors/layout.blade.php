<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title') - {{ config('app.name') }}</title>

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Icons -->
  @include('components.logos')
  <!-- END Icons -->

  <!-- Stylesheets -->
  <link rel="stylesheet" id="css-main" href="{{ asset('assets/templates/src/css/codebase.min.css') }}">
  <!-- END Stylesheets -->
</head>

<body>
  <!-- Page Container -->
  <div id="page-container" class="main-content-boxed">

    <!-- Main Container -->
    <main id="main-container">
      <!-- Page Content -->
      @yield('content')
      <!-- END Page Content -->
    </main>
    <!-- END Main Container -->
  </div>
  <!-- END Page Container -->

  <!-- Codebase JS -->
  <script src="{{ asset('assets/templates/src/js/codebase.app.min.js') }}"></script>
  <script src="{{ asset('assets/templates/src/js/lib/jquery.min.js') }}"></script>
</body>
</html>
