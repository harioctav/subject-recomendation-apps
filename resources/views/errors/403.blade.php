@extends('errors::layout')
@section('title', trans('Forbidden'))
@section('content')
<div class="hero bg-body-extra-light">
  <div class="hero-inner">
    <div class="content content-full">
      <div class="py-4 text-center">
        <div class="display-1 fw-bold text-corporate">
          <i class="fa fa-ban opacity-50 me-2"></i>
          {{ trans('403') }}
        </div>
        <h1 class="fw-bold mt-5 mb-2">
          {{ trans('Oops.. Anda baru saja menemukan halaman kesalahan..') }}
        </h1>
        <h2 class="fs-4 fw-medium text-muted mb-5">
          {{ trans($exception->getMessage() ?: 'Pengguna tidak memiliki izin yang tepat..') }}
        </h2>
        <a class="btn btn-lg btn-alt-secondary" href="{{ route('home') }}">
          <i class="fa fa-arrow-left opacity-50 me-2"></i>
          {{ trans('Kembali Ke Beranda') }}
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
