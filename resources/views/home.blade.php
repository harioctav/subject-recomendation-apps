@extends('layouts.app')
@section('title', trans('page.overview.title'))
{{-- @section('hero')
<div class="content">
  <!-- Header -->
  <div class="block block-rounded bg-gd-dusk">
    <div class="block-content bg-white-5">
      <div class="py-4 text-center">
        <h1 class="h2 fw-bold text-white mb-2">{{ trans('Dashboard') }}</h1>
<h2 class="h5 fw-medium text-white-75">
  {{ $greeting }}. Saat ini anda login sebagai <span class="fw-semibold text-white">{{ me()->name }}</span>
</h2>
<!-- Datetime -->
<div class="fs-sm fw-semibold text-white" id="date"></div>
<div class="d-flex justify-content-center align-items-center text-white">
  <i class="fa fa-clock fa-sm"></i>
  <div class="fs-sm fw-semibold ms-2" id="clock"></div>
</div>
<!-- END Datetime -->
</div>
</div>
</div>
<!-- END Header -->
</div>
@endsection --}}
@section('content')
<h2 class="content-heading">
  <i class="fa fa-chart-line opacity-50 opacity-50 me-2"></i>
  {{ trans('Ringkasan Statistik') }}
</h2>
@if (session('status'))
<div class="row items-push">
  <div class="col-6 col-xl-6">
    @include('components.success-alert')
  </div>
</div>
@endif
<div class="row items-push">
  <div class="col-6 col-xl-3">
    <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
      <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
        <div class="d-none d-sm-block">
          <i class="fa fa-school-circle-check fa-2x opacity-25"></i>
        </div>
        <div>
          <div class="fs-3 fw-semibold">{{ $data['majors'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">
            {{ trans('page.majors.title') }}
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-6 col-xl-3">
    <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
      <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
        <div class="d-none d-sm-block">
          <i class="fa fa-book fa-2x opacity-25"></i>
        </div>
        <div>
          <div class="fs-3 fw-semibold">{{ $data['subjects'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">
            {{ trans('page.subjects.title') }}
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-6 col-xl-3">
    <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
      <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
        <div class="d-none d-sm-block">
          <i class="fa fa-graduation-cap fa-2x opacity-25"></i>
        </div>
        <div>
          <div class="fs-3 fw-semibold">{{ $data['students'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">
            {{ trans('page.students.title') }}
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-6 col-xl-3">
    <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
      <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
        <div class="d-none d-sm-block">
          <i class="fa fa-users fa-2x opacity-25"></i>
        </div>
        <div>
          <div class="fs-3 fw-semibold">{{ $data['users'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">
            {{ trans('page.users.title') }}
          </div>
        </div>
      </div>
    </a>
  </div>
</div>
@endsection
@vite('resources/js/home.js')
