@extends('layouts.app')
@section('title', trans('page.majors.title'))
@push('css')
<style>
  .select2-container {
    width: 100% !important;
  }

  #subjects {
    display: none;
  }

</style>
@endpush
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
            <div class="select2-wrapper" style="width: 100%;">
              <select class="form-select @error('subjects.*') is-invalid @enderror" id="subjects" name="subjects[]" multiple>
                @if(old('subjects'))
                @foreach(old('subjects') as $subjectId)
                <option value="{{ $subjectId }}" selected>{{ App\Models\Subject::find($subjectId)->name }}</option>
                @endforeach
                @endif
              </select>
            </div>
            @error('subjects.*')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

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
@push('javascript')
<script>
  const urlMajorSubjects = "{{ route('api.majors.major_subjects.index', ['major' => $major]) }}"

</script>
@endpush
@vite('resources/js/academics/major_subjects/input.js')
