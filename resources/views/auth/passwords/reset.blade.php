@extends('layouts.guest')
@section('title', trans('Reset Kata Sandi'))
@section('content')

<!-- Page Content -->
<div class="bg-gd-lake">
  <div class="hero-static content content-full bg-body-extra-light">
    <!-- Header -->
    <div class="py-4 px-1 text-center mb-4">
      <a class="link-fx fw-bold" href="">
        <img src="{{ asset('assets/images/logos/logo.png') }}" alt="Logo Universitas Terbuka" width="70">
        <br>
        <span class="fs-4 text-body-color">Universitas</span>
        <span class="fs-4">Terbuka</span>
      </a>
      <h1 class="h3 fw-bold mt-5 mb-2">Selamat Datang Kembali</h1>
      <h2 class="h5 fw-medium text-muted mb-0">Silahkan masuk ke aplikasi untuk melanjutkan</h2>
    </div>
    <!-- END Header -->

    <!-- Reminder Form -->
    <div class="row justify-content-center px-1">
      <div class="col-sm-8 col-md-6 col-xl-4">

        <form method="POST" action="{{ route('password.update') }}" onsubmit="return disableSubmitButton()">
          @csrf

          <input type="hidden" name="token" value="{{ $token }}">

          <div class="form-floating mb-4">
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" placeholder="{{ trans('Email') }}">
            <label class="form-label" for="email">{{ trans('Username or Email') }}</label>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-floating mb-4 password-form">
            <i class="icon far fa-eye-slash fa-lg toggle-password"></i>
            <input type="password" class="form-control" id="current_password" name="password" autocomplete="current-password" placeholder="Password" required>
            <label class="form-label" for="current_password">{{ trans('Password') }}</label>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-floating mb-4">
            <input type="password" class="form-control" id="new_password" name="password_confirmation" autocomplete="new-password" placeholder="Password" required>
            <label class="form-label" for="new_password">{{ trans('Konfirmasi Password') }}</label>
          </div>

          <div class="mb-4 space-y-2">
            <button type="submit" class="btn btn-lg btn-alt-primary w-100 py-3 fw-semibold" id="submit-button">
              {{ __('Ubah Kata Sandi') }}
            </button>
          </div>

        </form>
      </div>
    </div>
    <!-- END Reminder Form -->
  </div>
</div>
<!-- END Page Content -->
@endsection
