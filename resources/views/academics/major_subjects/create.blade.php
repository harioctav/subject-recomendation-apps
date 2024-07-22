@extends('layouts.app')
@section('title', trans('page.majors.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="block block-rounded mb-0">
      <div class="block-content block-content-full">
        <div class="py-2 justify-content-between align-items-sm-center">
          <div class="mb-3">
            <h1 class="h3 fw-extrabold mb-1">
              {{ trans('page.majors.title') }}
            </h1>
            <h2 class="fs-sm fw-medium text-muted mb-0">
              {{ trans('page.majors.subjects.create') }}
            </h2>
          </div>
          <div class="mb-0">
            <a href="{{ route('majors.show', $major) }}" class="btn btn-sm btn-danger">
              <i class="fa fa-xs fa-chevron-left me-1"></i>
              {{ trans('button.back') }} Ke Halaman Sebelumnya
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('Tambah Data Matakuliah Untuk Program Studi') }} {{ $major->name }}
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ route('majors.subjects.store', $major->uuid) }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf

      <div class="row items-push justify-content-center">
        <div class="col-md-6">

          <div class="mb-4">
            <label for="subjects" class="form-label">{{ trans('Matakuliah') }}</label>
            <span class="text-danger">*</span>
            <select class="js-select2 form-select @error('subjects') is-invalid @enderror" id="subjects" name="subjects[]" style="width: 100%;" data-placeholder="{{ trans('Pilih Matakuliah..') }}" multiple>
              <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
              @foreach ($subjects as $item)
              <option value="{{ $item->id }}" {{ (is_array(old('subjects')) && in_array($item->id, old('subjects'))) ? 'selected' : '' }}>
                {{ $item->name }}
              </option>
              @endforeach
            </select>
            @error('subjects')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- <div class="mb-4">
            <label for="semester" class="form-label">{{ trans('Semester') }}</label>
          <span class="text-danger">*</span>
          <select name="semester" id="semester" class="js-select2 form-select @error('semester') is-invalid @enderror" data-placeholder="{{ trans('Semester Matakuliah berada') }}" style="width: 100%;">
            <option></option>
            @foreach ($semesters as $value)
            <option value="{{ $value }}" @if (old('semester')==$value) selected @endif>{{ $value }}</option>
            @endforeach
          </select>
          @error('semester')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div> --}}

        <div class="mb-4">
          <label for="semester" class="form-label">{{ trans('Semester') }}</label>
          <span class="text-danger">*</span>
          <input type="number" max="8" min="1" step="1" name="semester" id="semester" value="{{ old('semester') }}" class="form-control @error('semester') is-invalid @enderror" placeholder="{{ trans('Masukkan Semester') }}" onkeypress="return onlyNumber(event)">
          @error('semester')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-4">
          <button type="submit" class="btn btn-alt-primary w-100" id="submit-button">
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
