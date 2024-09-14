<nav id="sidebar">
  <div class="sidebar-content">
    <!-- Side Header -->
    <div class="content-header justify-content-lg-center">
      <!-- Logo -->
      <div>
        <span class="smini-visible fw-bold tracking-wide fs-lg">
          U<span class="text-primary">T</span>
        </span>
        <a class="link-fx fw-bold tracking-wide mx-auto" href="javascript:void(0)">
          <span class="smini-hidden">
            <img src="{{ asset('assets/images/logos/logo.png') }}" alt="Logo Universitas Terbuka" width="32">
            <span class="fs-6 text-dual">
              <small class="text-center">Universitas Terbuka</small>
            </span>
          </span>
        </a>
      </div>
      <!-- END Logo -->

      <!-- Options -->
      <div>
        <!-- Close Sidebar, Visible only on mobile screens -->
        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
        <button type="button" class="btn btn-sm btn-alt-danger d-lg-none" data-toggle="layout" data-action="sidebar_close">
          <i class="fa fa-fw fa-times"></i>
        </button>
        <!-- END Close Sidebar -->
      </div>
      <!-- END Options -->
    </div>
    <!-- END Side Header -->

    <!-- Sidebar Scrolling -->
    <div class="js-sidebar-scroll">

      <!-- Side User -->
      <div class="content-side content-side-user px-0 py-0">
        <!-- Visible only in mini mode -->
        <div class="smini-visible-block animated fadeIn px-3">
          <img class="img-avatar img-avatar32" src="{{ me()->getUserAvatar() }}" alt="">
        </div>
        <!-- END Visible only in mini mode -->

        <!-- Visible only in normal mode -->
        <div class="smini-hidden text-center mx-auto">
          <a class="img-link" href="">
            <img class="img-avatar" src="{{ me()->getUserAvatar() }}" alt="">
          </a>
          <ul class="list-inline mt-3 mb-0">
            <li class="list-inline-item">
              <a class="link-fx text-dual fs-sm fw-semibold text-uppercase" href="{{ route('users.show', me()->uuid) }}">
                {{ me()->name }}
              </a>
            </li>
            <li class="list-inline-item">
              <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
              <a class="link-fx text-dual" data-toggle="layout" data-action="dark_mode_toggle" href="javascript:void(0)">
                <i class="fa fa-burn"></i>
              </a>
            </li>
            <li class="list-inline-item">
              <a class="link-fx text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-input').submit();">
                <i class="fa fa-sign-out-alt"></i>
              </a>

              <form id="logout-input" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </li>
          </ul>
        </div>
        <!-- END Visible only in normal mode -->
      </div>
      <!-- END Side User -->

      <!-- Side Navigation -->
      <div class="content-side content-side-full">
        <ul class="nav-main">
          <li class="nav-main-item">
            <a class="nav-main-link {{ Request::is('home*') ? 'active' : '' }}" href="{{ route('home') }}">
              <i class="nav-main-link-icon fa fa-house-user"></i>
              <span class="nav-main-link-name">{{ trans('page.overview.title') }}</span>
            </a>
          </li>

          @canany(['majors.index', 'students.index', 'subjects.index'])
          <li class="nav-main-heading">{{ trans('Akademik') }}</li>
          <li class="nav-main-item {{ Request::is('academics*') ? 'open' : '' }}">
            <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ Request::is('academics*') ? 'true' : 'false' }}" href="#">
              <i class="nav-main-link-icon fa fa-graduation-cap"></i>
              <span class="nav-main-link-name">{{ trans('Akademik') }}</span>
            </a>
            <ul class="nav-main-submenu">
              @can('students.index')
              <li class="nav-main-item">
                <a class="nav-main-link {{ Request::is('academics/students*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                  <span class="nav-main-link-name">{{ trans('page.students.title') }}</span>
                </a>
              </li>
              @endcan
              @can('majors.index')
              <li class="nav-main-item">
                <a class="nav-main-link {{ Request::is('academics/majors*') ? 'active' : '' }}" href="{{ route('majors.index') }}">
                  <span class="nav-main-link-name">{{ trans('page.majors.title') }}</span>
                </a>
              </li>
              @endcan
              @can('subjects.index')
              <li class="nav-main-item">
                <a class="nav-main-link {{ Request::is('academics/subjects*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                  <span class="nav-main-link-name">{{ trans('page.subjects.title') }}</span>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endcan

          @canany(['recommendations.index', 'grades.index'])
          <li class="nav-main-heading">{{ trans('Evaluations') }}</li>
          <li class="nav-main-item {{ Request::is('evaluations*') ? 'open' : '' }}">
            <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ Request::is('evaluations*') ? 'true' : 'false' }}" href="#">
              <i class="nav-main-link-icon fa fa-bookmark fa-sm"></i>
              <span class="nav-main-link-name">{{ trans('Rekomendasi') }}</span>
            </a>
            <ul class="nav-main-submenu">
              @can('grades.index')
              <li class="nav-main-item">
                <a class="nav-main-link {{ Request::is('evaluations/grades*') ? 'active' : '' }}" href="{{ route('grades.index') }}">
                  <span class="nav-main-link-name">{{ trans('page.grades.title') }}</span>
                </a>
              </li>
              @endcan
              @can('recommendations.index')
              <li class="nav-main-item">
                <a class="nav-main-link {{ Request::is('evaluations/recommendations*') ? 'active' : '' }}" href="{{ route('recommendations.index') }}">
                  <span class="nav-main-link-name">{{ trans('page.recommendations.title') }}</span>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endcanany

          @canany(['roles.index', 'users.index'])
          <li class="nav-main-heading">{{ trans('Management') }}</li>

          <li class="nav-main-item {{ Request::is('settings*') ? 'open' : '' }}">
            <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ Request::is('settings*') ? 'true' : 'false' }}" href="#">
              <i class="nav-main-link-icon fa fa-cog"></i>
              <span class="nav-main-link-name">{{ trans('Pengaturan') }}</span>
            </a>
            <ul class="nav-main-submenu">
              @can('users.index')
              <li class="nav-main-item">
                <a class="nav-main-link {{ Request::is('settings/users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                  <span class="nav-main-link-name">{{ trans('page.users.title') }}</span>
                </a>
              </li>
              @endcan
              @can('roles.index')
              <li class="nav-main-item">
                <a class="nav-main-link {{ Request::is('settings/roles*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                  <span class="nav-main-link-name">{{ trans('page.roles.title') }}</span>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endcan

          @can('activities.index')
          <li class="nav-main-item">
            <a class="nav-main-link {{ Request::is('activities*') ? 'active' : '' }}" href="{{ route('activities.index') }}">
              <i class="nav-main-link-icon fa fa-snowboarding"></i>
              <span class="nav-main-link-name">{{ trans('page.activities.title') }}</span>
            </a>
          </li>
          @endcan

        </ul>
      </div>
      <!-- END Side Navigation -->
    </div>
    <!-- END Sidebar Scrolling -->

  </div>
</nav>
