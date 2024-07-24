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

      <div class="row justify-content-center">
        <div class="col-md-6">

          <div class="mb-4">
            <label for="major_id" class="form-label">{{ trans('Program Studi') }}</label>
            <span class="text-danger">*</span>
            <select name="major_id" id="major_id" class="js-select2 form-select @error('major_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Program Studi') }}" style="width: 100%;" data-old="{{ old('major_id') }}">
              <option></option>
              @foreach ($majors as $item)
              <option value="{{ $item->id }}" data-uuid="{{ $item->uuid }}" @if (old('major_id')==$item->id) selected @endif>{{ $item->name }}</option>
              @endforeach
            </select>
            @error('major_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="student_id" class="form-label">{{ trans('Pilih Mahasiswa') }}</label>
            <span class="text-danger">*</span>
            <select name="student_id" id="student_id" class="js-select2 form-select @error('student_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('student_id') }}">
              <option></option>

            </select>
            @error('student_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="subject_id" class="form-label">{{ trans('Pilih Matakuliah') }}</label>
            <span class="text-danger">*</span>
            <select name="subject_id" id="subject_id" class="js-select2 form-select @error('subject_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('subject_id') }}">
              <option></option>

            </select>
            @error('subject_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="grade" class="form-label">{{ trans('Nilai') }}</label>
            <span class="text-danger">*</span>
            <input type="number" min="1" step="1" name="grade" id="grade" value="{{ old('grade') }}" class="form-control @error('grade') is-invalid @enderror" placeholder="{{ trans('Masukkan Nilai') }}" onkeypress="return onlyNumber(event)">
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
  var studentURL = "{{ route('api.students.index', ['major_id' => ':major_id']) }}"
  var subjectURL = "{{ route('api.subjects.index', ['student' => ':student']) }}"

</script>
@endpush
