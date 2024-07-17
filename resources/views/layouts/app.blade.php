<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Icons -->
  @include('components.logos')
  <!-- END Icons -->

  <!-- Styles -->
  @include('components.style')
  <!-- Styles -->

  <!-- Vite Builder -->
  @vite([])
</head>
<body>
  <!-- Page Container -->
  <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">

    <!-- Sidebar -->
    @include('partials.sidebar')
    <!-- END Sidebar -->

    <!-- Header -->
    <header id="page-header">
      @include('partials.header')
    </header>
    <!-- END Header -->

    <!-- Main Container -->
    <main id="main-container">
      <!-- If u want to add hero page -->
      @yield('hero')
      <!-- Page Content -->
      <div class="content">
        @yield('content')
      </div>
      <!-- END Page Content -->
    </main>
    <!-- END Main Container -->

    <!-- Footer -->
    <footer id="page-footer">
      @include('partials.footer')
    </footer>
    <!-- END Footer -->
  </div>
  <!-- END Page Container -->

  @include('components.javascript')
</body>
</html>
