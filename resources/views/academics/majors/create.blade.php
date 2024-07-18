@extends('layouts.app')
@section('title', trans('page.majors.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.majors.title') }}
      <a href="{{ route('majors.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('majors.create') }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.majors.create') }}
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ route('majors.store') }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf
      <div class="row items-push justify-content-center">
        <div class="col-md-6">
          {{-- Code --}}
          <div class="mb-4">
            <label for="code" class="form-label">{{ trans('Kode') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="code" id="code" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror" placeholder="{{ trans('Kode Jurusan') }}">
            @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Code --}}

          {{-- Nama --}}
          <div class="mb-4">
            <label for="name" class="form-label">{{ trans('Jurusan') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="{{ trans('Nama Jurusan') }}" onkeypress="return onlyLetter(event)">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Nama --}}

          {{-- Jenjang --}}
          <div class="mb-4">
            <label for="degree" class="form-label">{{ trans('Jenjang atau Tingkatan') }}</label>
            <span class="text-danger">*</span>
            <select name="degree" id="degree" class="js-select2 form-select @error('degree') is-invalid @enderror" data-placeholder="{{ trans('Pilih Jenjang atau Tingkatan') }}" style="width: 100%;">
              <option></option>
              @foreach ($degrees as $item)
              <option value="{{ $item }}">{{ $item }}</option>
              @endforeach
            </select>
            @error('degree')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Jenjang --}}

          <div class="mb-4">
            <button type="submit" class="btn btn-primary w-100" id="submit-button">
              <i class="fa fa-fw fa-circle-check me-1"></i>
              {{ trans('button.create') }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
