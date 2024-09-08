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
      {{ Breadcrumbs::render('grades.show', $student) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.grades.show') }}
    </h3>
  </div>
  <div class="block-content">
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
      </div>
    </div>

    <div class="mb-3">
      @can('grades.export')
      <a href="{{ route('grades.export', $student) }}" target="_blank" class="btn btn-sm btn-success">
        <i class="fa fa-print fa-sm me-1"></i>
        {{ trans('Cetak Transkrip Nilai') }}
      </a>
      @endcan

      @can('grades.create')
      <a href="{{ route('grades.create', $student) }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.grades.create') }}
      </a>
      @endcan
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="mb-0">
          <label for="grade" class="form-label">{{ trans('Filter Berdasarkan Nilai') }}</label>
          <select type="text" class="form-select" name="grade" id="grade">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach (GradeType::toArray() as $item)
            <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="my-3">
      {{ $dataTable->table() }}
    </div>
  </div>
</div>
@endsection
@push('javascript')
{{ $dataTable->scripts() }}
@vite('resources/js/evaluations/grades/show.js')

<script>
  var urlDestroy = "{{ route('grades.destroy', ':uuid') }}"

</script>
@endpush
