@extends('layouts.app')
@section('title', trans('page.recommendations.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.recommendations.title') }}
      <a href="{{ route('recommendations.show',  $student) }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('recommendations.create', $student) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.recommendations.create') }}
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ route('recommendations.store', $student) }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf

      <input type="hidden" name="student_id" value="{{ $student->id }}">

      {{-- Student info --}}
      @includeIf('components.students.info')
      {{-- Student info --}}

      <div class="row justify-content-end">
        <div class="col-lg-8">
          <div class="mb-3">
            <label for="course_credit_selected" class="form-label">{{ trans('Jumlah SKS Diambil') }}</label>
            <input type="number" max="24" step="1" min="3" name="course_credit_selected" id="course_credit_selected" value="{{ old('course_credit_selected') }}" class="form-control @error('course_credit_selected') is-invalid @enderror" placeholder="{{ trans('Jumlah SKS Dipilih') }}" readonly disabled>
          </div>

          @if($detail['has_grade_e'])
          <div class="alert alert-warning d-flex align-items-center alert-dismissible" role="alert">
            <div class="flex-shrink-0">
              <i class="fa fa-fw fa-exclamation"></i>
            </div>
            <div class="flex-grow-1 ms-3">
              <p class="mb-0">
                {{ trans('Mahasiswa ini memiliki nilai E, tolong lakukan rekomendasi ulang untuk memperbaiki nilai E tersebut.') }}
              </p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

        </div>
        <div class="col-lg-4">
          <div class="mb-3">
            <label for="search_custom" class="form-label">Cari Berdasarkan Kode Matakuliah</label>
            <input type="text" class="form-control" id="search_custom" name="search_custom" placeholder="Search...">
          </div>
          <div class="mb-0">
            <label for="grade_filter" class="form-label">{{ trans('Filter Berdasarkan Nilai') }}</label>
            <select class="form-select" name="grade_filter" id="grade_filter">
              <option value="">All</option>
              @foreach (GradeType::toArray() as $item)
              <option value="{{ $item }}">{{ $item }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>


      <div class="my-3">
        {{-- <table id="coursesTable" class="table table-bordered table-vcenter table-hover">
          <thead>
            <tr>
              <th><input type="checkbox" id="select-all"></th>
              <th>Semester</th>
              <th>Kode</th>
              <th>Matakuliah</th>
              <th>Nilai</th>
              <th>SKS</th>
              <th>Ket. Matkul</th>
              <th>Ket. Rekomendasi</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table> --}}

        {{ $dataTable->table() }}
      </div>

      <div class="row">
        <div class="col-lg-6">
          <div class="mb-4">
            <label for="exam_period" class="form-label">{{ trans('Masa Ujian') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="exam_period" id="exam_period" value="{{ old('exam_period', $student->initial_registration_period ? $student->formatted_registration_period : null) }}" class="form-control @error('exam_period') is-invalid @enderror" placeholder="{{ trans('Masukkan Masa Ujian') }}">
            <span class="text-muted">
              <small>
                <em>{{ __('Ubah masa ujian sesuai waktu Semester Berjalan atau Semester Berikutnya') }}</em>
              </small>
            </span>
            @error('exam_period')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="note" class="form-label">{{ trans('Catatan Rekomendasi') }}</label>
            <span class="text-danger">*</span>
            <select name="note" id="note" class="js-select2 form-select @error('note') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;">
              <option></option>
              @foreach (RecommendationStatus::toArray(0, 1, 3, 6) as $value)
              <option value="{{ $value }}" @if (old('note')==$value) selected @endif>{{ ucfirst($value) }}</option>
              @endforeach
            </select>
            <span class="text-muted">
              <small>
                <em>{{ __('Pilih sesuai dengan Masa Ujian yang anda masukkan') }}</em>
              </small>
            </span>
            @error('note')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <button type="submit" class="btn btn-alt-primary w-100" id="button-submit">
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
@push('javascript')
@vite('resources/js/evaluations/recommendations/input.js')
{{ $dataTable->scripts() }}
@endpush
