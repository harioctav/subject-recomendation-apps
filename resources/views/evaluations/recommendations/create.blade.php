@extends('layouts.app')
@section('title', trans('page.recommendations.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.recommendations.title') }}
      <a href="{{ route('recommendations.index') }}" class="btn btn-sm btn-block-option text-danger">
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

      <div class="row items-push">
        <div class="col-lg-4">

          <a class="block block-rounded block-link-shadow bg-info" href="javascript:void(0)">
            <div class="block-content block-content-full d-flex justify-content-between align-items-center">
              <div>
                <img class="img-avatar" src="{{ $student->getAvatar() }}" alt="Foto Mahasiswa">
              </div>
              <div class="text-end">
                <div class="fw-semibold text-white mb-1">{{ $student->name }}</div>
                <div class="fs-sm text-white-75">{{ $student->nim }}</div>
              </div>
            </div>
          </a>

          <ul class="list-group push">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Program Studi') }}
              <span class="fw-semibold text-end">{{ $student->major->name }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Status Mahasiswa') }}
              <span class="fw-semibold text-end">{!! $student->statusLabel !!}</span>
            </li>
          </ul>

        </div>
        <div class="col-lg-7 offset-lg-1">
          <ul class="list-group push">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Total SKS WAJIB Ditempuh') }}
              <span class="fw-semibold">{{ $data['total_course_credit'] }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Total SKS SUDAH Ditempuh') }}
              <span class="fw-semibold">{{ $data['total_course_credit_done'] }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Sisa SKS Belum Ditempuh') }}
              <span class="fw-semibold">{{ $data['total_course_credit_remainder'] }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Indeks Prestasi Kumulatif') }}
              <span class="fw-semibold">??</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <div class="mb-3">
            <label for="course_credit_selected" class="form-label">{{ trans('Jumlah SKS Diambil') }}</label>
            <input type="number" max="24" step="1" min="3" name="course_credit_selected" id="course_credit_selected" value="{{ old('course_credit_selected') }}" class="form-control @error('course_credit_selected') is-invalid @enderror" placeholder="{{ trans('Jumlah SKS Dipilih') }}" readonly disabled>
          </div>

          <div class="mb-3">
            <label for="course_credit" class="form-label">{{ trans('Filter Berdasarkan Jumlah SKS') }}</label>
            <input type="number" max="24" step="1" min="3" name="course_credit" id="course_credit" value="{{ old('course_credit') }}" class="form-control @error('course_credit') is-invalid @enderror" placeholder="{{ trans('Masukkan Jumlah SKS') }}">
          </div>

          <div class="alert alert-danger d-none" id="sks-error-message">Total SKS tidak boleh melebihi 24. Mohon kurangi pilihan mata kuliah.</div>

        </div>
      </div>

      <div class="my-3">
        <table id="coursesTable" class="table table-bordered table-vcenter table-hover">
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
        </table>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <div class="mb-4">
            <label for="exam_period" class="form-label">{{ trans('Masa Ujian') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="exam_period" id="exam_period" value="{{ old('exam_period') }}" class="form-control @error('exam_period') is-invalid @enderror" placeholder="{{ trans('Masukkan Masa Ujian') }}">
            @error('exam_period')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="note" class="form-label">{{ trans('Catatan Rekomendasi') }}</label>
            <span class="text-danger">*</span>
            <select name="note" id="note" class="js-select2 form-select @error('note') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;">
              <option></option>
              @foreach (RecommendationNote::toArray(0,2) as $value)
              <option value="{{ $value }}" @if (old('note')==$value) selected @endif>{{ ucfirst(trans($value)) }}</option>
              @endforeach
            </select>
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
@vite('resources/js/evaluations/recommendations/create.js')

<script>
  var datatableURL = "{{ route('api.students.courses', ['student' => $student]) }}"
  var statusRecommendation = "{{ RecommendationNote::REPAIR->value }}"

</script>
@endpush
