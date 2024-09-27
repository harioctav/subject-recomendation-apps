@extends('layouts.app')
@section('title', trans('page.users.edit'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.users.title') }}
      <a href="{{ route('users.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('users.edit', $user) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.users.edit', [
        'users' => trans('page.users.title')
      ]) }}
    </h3>
  </div>
  <div class="block-content block-content-full">

    <form action="{{ route('users.update', $user->uuid) }}" method="POST" enctype="multipart/form-data" onsubmit="return disableSubmitButton()">
      @csrf
      @method('PATCH')

      <div class="row items-push">
        <div class="col-lg-3">
          <p class="text-muted">
            {!! trans("page.warning.password") !!}
          </p>
        </div>
        <div class="col-lg-7 offset-lg-1">
          {{-- Name --}}
          <div class="mb-4">
            <label for="name" class="form-label">{{ trans('Nama') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="{{ trans('Nama Lengkap') }}" onkeypress="return onlyLetter(event)">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Email --}}
          <div class="mb-4">
            <label for="email" class="form-label">{{ trans('Email') }}</label>
            <span class="text-danger">*</span>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required placeholder="{{ trans('Alamat Email') }}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Role --}}
          <div class="mb-4">
            <label for="roles" class="form-label">{{ trans('Peran Pengguna') }}</label>
            <span class="text-danger">*</span>
            <select name="roles" id="roles" class="js-select2 form-select @error('roles') is-invalid @enderror" data-placeholder="{{ trans('Pilih Peran') }}" style="width: 100%;">
              <option></option>
              @foreach ($roles as $item)
              <option value="{{ $item->id }}" @if (old('roles', $user->getRoleId())==$item->id) selected @endif>{{ $item->name }}</option>
              @endforeach
            </select>
            @error('roles')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Avatar --}}
          <div class="row mb-0">
            <div class="col-md-10 col-xl-6">
              <div class="push">
                <img class="img-prev img-avatar" src="{{ $user->getUserAvatar() }}" alt="">
              </div>
              <div class="mb-4">
                <label class="form-label" for="image">{{ trans('Pilih Foto Profil Baru') }}</label>
                <input class="form-control @error('file') is-invalid @enderror" type="file" accept="image/*" id="image" name="file" onchange="return previewImage()">
                @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          {{-- Phone --}}
          <div class="mb-4">
            <label for="phone" class="form-label">{{ trans('Telepon') }}</label>
            <small class="text-muted"><em>Opsional</em></small>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror" placeholder="{{ trans('Telepon') }}" onkeypress="return onlyNumber(event)">
            @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <button type="submit" class="btn btn-alt-warning w-100" id="submit-button">
              <i class="fa fa-fw fa-circle-check me-1"></i>
              {{ trans('button.edit') }}
            </button>
          </div>
        </div>
      </div>

    </form>

  </div>
</div>
@endsection
