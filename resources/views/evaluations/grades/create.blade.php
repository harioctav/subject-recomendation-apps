@extends('layouts.app')
@section('title', trans('page.grades.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.grades.title') }}
      <a href="{{ route('grades.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('grades.create', $student) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.grades.create') }}
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ route('grades.store', $student) }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf

      <div class="row items-push">
        <div class="col-lg-3">
          <p class="text-muted">
            {{ trans('Untuk menambahkan nilai Mahasiswa silahkan mengecek kembali pada halaman Rekomendasi, apakah sudah di rekomendasikan atau belum agar Matakuliah muncul dan bisa diberi nilai.') }}
          </p>
        </div>
        <div class="col-lg-7 offset-1">

          <input type="hidden" name="student_id" value="{{ $student->id }}">

          <ul class="list-group push">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Mahasiswa') }}
              <span id="student-name" class="fw-semibold">{{ $student->name }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('NIM') }}
              <span id="student-nim" class="fw-semibold">{{ $student->nim }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Program Studi') }}
              <span id="student-major" class="fw-semibold text-end" style="min-width: 0; flex: 1;">{{ $student->major->name }}</span>
            </li>
          </ul>

          {{-- <div class="mb-4">
            <label for="subject_id" class="form-label">{{ trans('Pilih Matakuliah') }}</label>
          <span class="text-danger">*</span>
          <select name="subject_id" id="subject_id" class="js-select2 form-select @error('subject_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;">
            <option></option>
            @foreach ($recommendations['subjects'] as $group)
            <optgroup label="{{ $group['semester'] }}">
              @foreach ($group['subjects'] as $subject)
              <option value="{{ $subject['id'] }}" {{ old('subject_id') == $subject['id'] ? 'selected' : '' }}>
                {{ $subject['code'] }} - {{ $subject['name'] }} - {{ $subject['exam_period'] }}
              </option>
              @endforeach
            </optgroup>
            @endforeach
          </select>
          @error('subject_id')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div> --}}

        <div class="mb-4">
          <label for="subjects" class="form-label">{{ trans('Matakuliah') }}</label>
          <span class="text-danger">*</span>
          <select class="js-select2 form-select @error('subjects.*') is-invalid @enderror" id="subjects" name="subjects[]" style="width: 100%;" data-placeholder="{{ trans('Pilih Matakuliah..') }}" multiple>
            <option></option>
            @foreach ($recommendations['subjects'] as $group)
            <optgroup label="{{ $group['semester'] }}">
              @foreach ($group['subjects'] as $subject)
              <option value="{{ $subject['id'] }}" {{ (is_array(old('subjects')) && in_array($subject['id'], old('subjects'))) ? 'selected' : '' }}>
                {{ $subject['code'] }} - {{ $subject['name'] }} - {{ $subject['exam_period'] }}
              </option>
              @endforeach
            </optgroup>
            @endforeach
          </select>
          @error('subjects')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-4">
          <label for="grade" class="form-label">{{ trans('Nilai') }}</label>
          <span class="text-danger">*</span>
          <select name="grade" id="grade" class="js-select2 form-select @error('grade') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;">
            <option></option>
            @foreach ($grades as $value)
            <option value="{{ $value }}" @if (old('grade')==$value) selected @endif>{{ ucfirst(trans($value)) }}</option>
            @endforeach
          </select>
          @error('grade')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-4">
          <label for="mutu" class="form-label">{{ trans('Nilai Mutu') }}</label>
          <input type="number" min="1" step="0.5" name="mutu" id="mutu" value="{{ old('mutu') }}" class="form-control @error('mutu') is-invalid @enderror" placeholder="{{ trans('Nilai Mutu Ujian') }}">
          @error('mutu')
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
