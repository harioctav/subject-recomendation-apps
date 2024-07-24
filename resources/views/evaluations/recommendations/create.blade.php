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
      {{ Breadcrumbs::render('recommendations.create') }}
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
    <form action="{{ route('recommendations.store') }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf

      <div class="row items-push">
        <div class="col-lg-3">
          <p class="text-muted">
            Pilih mahasiswa dan mata kuliah yang direkomendasikan.
          </p>
        </div>
        <div class="col-lg-7 offset-1">
          <div class="mb-4">
            <label for="student_id" class="form-label">{{ trans('Mahasiswa') }}</label>
            <span class="text-danger">*</span>
            <select name="student_id" id="student_id" class="js-select2 form-select @error('student_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Mahasiswa') }}" style="width: 100%;" data-old="{{ old('student_id') }}">
              <option></option>
              @foreach ($students as $item)
              <option value="{{ $item->id }}" @if (old('student_id')==$item->id) selected @endif>
                {{ $item->name }}
              </option>
              @endforeach
            </select>
            @error('student_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <ul class="list-group push">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('NIM') }}
              <span class="fw-semibold">--</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Program Studi') }}
              <span class="fw-semibold">--</span>
            </li>
          </ul>

          <div class="loading-indicator" style="display: none;">Loading...</div>
          <div class="card-container" style="display: none;"></div>

          <div class="mb-4">
            <button type="submit" class="btn btn-alt-primary w-100">
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

<script>
  var studentURL = "{{ route('api.students.courses', ':student_id') }}";
  var studentDetailURL = "{{ route('api.students.show', ':student_id') }}";

</script>
@endpush
