<!-- Header Content -->
<div class="content-header">
  <!-- Left Section -->
  <div class="space-x-1">
    <!-- Toggle Sidebar -->
    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
    <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
      <i class="fa fa-fw fa-bars"></i>
    </button>
    <!-- END Toggle Sidebar -->
  </div>
  <!-- END Left Section -->

  <!-- Right Section -->
  <div class="space-x-1">
    <!-- User Dropdown -->
    <div class="dropdown d-inline-block">
      <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-user d-sm-none"></i>
        <span class="d-none d-sm-inline-block fw-semibold">{{ me()->name }}</span>
        <i class="fa fa-angle-down opacity-50 ms-1"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
        <div class="px-2 py-3 bg-body-light rounded-top">
          <h5 class="h6 text-center mb-0">
            {{ me()->name }}
          </h5>
        </div>
        <div class="p-2">
          @can('users.show')
          <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1 {{ Request::is('users/show/*') ? 'active' : '' }}" href="{{ route('users.show', me()->uuid) }}">
            <span>{{ trans('Profil') }}</span>
            <i class="fa fa-fw fa-user opacity-25"></i>
          </a>
          @endcan
          @can('users.password')
          <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1 {{ Request::is('users/password/*') ? 'active' : '' }}" href="{{ route('users.password', me()->uuid) }}">
            <span>{{ trans('Ubah Kata Sandi') }}</span>
            <i class="fa fa-fw fa-lock opacity-25"></i>
          </a>
          @endcan

          <div class="dropdown-divider"></div>

          <a class="dropdown-item d-flex align-items-center text-danger justify-content-between space-x-1" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span>{{ trans('Keluar Aplikasi') }}</span>
            <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
          </a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </div>
      </div>
    </div>
    <!-- END User Dropdown -->
  </div>
  <!-- END Right Section -->
</div>
<!-- END Header Content -->

<!-- Header Loader -->
<!-- Please check out the Activity page under Elements category to see examples of showing/hiding it -->
<div id="page-header-loader" class="overlay-header bg-primary">
  <div class="content-header">
    <div class="w-100 text-center">
      <i class="far fa-sun fa-spin text-white"></i>
    </div>
  </div>
</div>
<!-- END Header Loader -->
