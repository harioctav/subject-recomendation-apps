@extends('layouts.app')
@section('title', trans('Ubah Kata Sandi'))
@section('hero')
<!-- User Info -->
<div class="bg-image bg-image-center" style="background-image: url({{ asset('assets/images/backgrounds/bg-password.png') }});">
  <div class="bg-black-75 py-4">
    <div class="content content-full text-center">
      <!-- Avatar -->
      <div class="mb-3">
        <a class="img-link" href="">
          <img class="img-avatar img-avatar96 img-avatar-thumb" src="{{ $user->getUserAvatar() }}" alt="">
        </a>
      </div>
      <!-- END Avatar -->

      <!-- Personal -->
      <h1 class="h3 text-white fw-bold mb-2">{{ $user->name }}</h1>
      <h2 class="h5 text-white-75">
        {{ $user->getRoleName() }}
      </h2>
      <!-- END Personal -->

      <!-- Actions -->
      <a href="{{ route('home') }}" class="btn btn-danger">
        <i class="fa fa-arrow-left fa-sm opacity-50 me-2"></i>
        {{ trans('Kembali ke Dashboard') }}
      </a>
      <!-- END Actions -->
    </div>
  </div>
</div>
<!-- END User Info -->
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      <i class="fa fa-asterisk me-2 text-muted"></i>
      {{ trans('Ubah Kata Sandi') }}
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ url('users/password') }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf

      <div class="row items-push">
        <div class="col-lg-3">
          <p class="text-muted">
            {{ trans('Mengubah kata sandi masuk Anda adalah cara mudah untuk menjaga keamanan akun Anda.') }}
          </p>
        </div>
        <div class="col-lg-7 offset-lg-1">
          <div class="mb-4 password-form">
            <label class="form-label" for="current_password">{{ trans('Kata Sandi Saat Ini') }}</label>
            <span class="text-danger">*</span>
            <div class="input-group">
              <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
              <span class="input-group-text">
                <i class="far fa-eye-slash toggle-password" style="cursor: pointer"></i>
              </span>
              @error('current_password')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-4 password-form">
            <label class="form-label" for="password">{{ trans('Kata Sandi Baru') }}</label>
            <span class="text-danger">*</span>
            <div class="input-group">
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
              <span class="input-group-text">
                <i class="far fa-eye-slash toggle-password" style="cursor: pointer"></i>
              </span>
              @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label" for="password_confirmation">{{ trans('Konfirmasi Kata Sandi Baru') }}</label>
            <span class="text-danger">*</span>
            <input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation">
            @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-4">
            <button type="submit" class="btn btn-alt-warning w-100" id="submit-button">
              {{ trans('Ubah Kata Sandi') }}
            </button>
          </div>
        </div>

    </form>
  </div>
</div>
@endsection
