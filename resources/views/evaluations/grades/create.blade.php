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
      {{ Breadcrumbs::render('grades.create') }}
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
    <form action="{{ route('grades.store') }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf

      <div class="row items-push">
        <div class="col-lg-3">
          <p class="text-muted">
            {{ trans('Untuk menambahkan nilai Mahasiswa silahkan mengecek kembali pada halaman Rekomendasi, apakah sudah di rekomendasikan atau belum agar Matakuliah muncul dan bisa diberi nilai.') }}
          </p>
        </div>
        <div class="col-lg-7 offset-1">

          <div class="mb-4">
            <label for="student_id" class="form-label">{{ trans('Mahasiswa') }}</label>
            <span class="text-danger">*</span>
            <select name="student_id" id="student_id" class="js-select2 form-select @error('student_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Mahasiswa') }}" style="width: 100%;" data-old="{{ old('student_id') }}">
              <option></option>
              @foreach ($students as $item)
              <option value="{{ $item->id }}" data-uuid="{{ $item->uuid }}" @if (old('student_id')==$item->id) selected @endif>{{ $item->name }}</option>
              @endforeach
            </select>
            @error('student_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <ul class="list-group push">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('NIM') }}
              <span id="student-nim" class="fw-semibold">--</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Program Studi') }}
              <span id="student-major" class="fw-semibold text-end" style="min-width: 0; flex: 1;">--</span>
            </li>
          </ul>

          <div class="mb-4">
            <label for="subject_id" class="form-label">{{ trans('Pilih Matakuliah') }}</label>
            <span class="text-danger">*</span>
            <select name="subject_id" id="subject_id" class="js-select2 form-select @error('subject_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('subject_id') }}">
              <option></option>
              <!-- Subjects will be populated by JavaScript -->
            </select>
            @error('subject_id')
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
@push('javascript')
@vite('resources/js/evaluations/grades/input.js')

<script>
  var studentURL = "{{ route('api.students.index', ['student' => ':student']) }}"

  // Simpan data NIM dan Program Studi jika ada error validasi
  @if(old('student_id'))
  $("#student-nim").text("{{ old('student_nim', '--') }}");
  $("#student-major").text("{{ old('student_major', '--') }}");
  @endif

</script>
@endpush
