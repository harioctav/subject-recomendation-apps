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
      {{ Breadcrumbs::render('grades.edit', $grade) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.grades.create') }} Step Kedua
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ route('grades.update', $grade->uuid) }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf
      @method('PATCH')

      <input type="hidden" name="major_id" value="{{ $grade->student->major->id }}">
      <input type="hidden" name="student_id" value="{{ $grade->student->id }}">
      <input type="hidden" name="subject_id" value="{{ $grade->subject->id }}">

      <div class="row items-push justify-content-center">
        <div class="col-lg-7">
          <h6 class="mb-3">{{ trans('Program Studi') }}</h6>
          <ul class="list-group push">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Nama Program Studi') }}
              <span class="fw-semibold">{{ $grade->student->major->name }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Matakuliah') }}
              <span class="fw-semibold flex-grow-1 text-end">{{ $grade->subject->name }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Semester') }}
              <span class="fw-semibold flex-grow-1 text-end">{{ $grade->semester }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Waktu Ujian') }}
              <span class="fw-semibold flex-grow-1 text-end">{{ $grade->subject->exam_time }}</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-6">

          <div class="py-1 mt-1 mt-lg-3 mb-1">
            <p class="fs-sm text-uppercase fw-bold mb-1">
              Ubah Data Nilai Mahasiswa
            </p>
          </div>

          <div class="mb-4">
            <label for="grade" class="form-label">{{ trans('Nilai') }}</label>
            <span class="text-danger">*</span>
            <input type="number" max="100" min="1" step="1" name="grade" id="grade" value="{{ old('grade', $grade->grade) }}" class="form-control @error('grade') is-invalid @enderror" placeholder="{{ trans('Masukkan Nilai') }}" onkeypress="return onlyNumber(event)">
            @error('grade')
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
