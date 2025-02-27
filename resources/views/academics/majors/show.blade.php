@extends('layouts.app')
@section('title', trans('page.majors.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.majors.title') }}
      <a href="{{ route('majors.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('majors.show', $major) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.majors.show', ['majors' => trans('page.majors.title')]) }}
    </h3>
  </div>
  <div class="block-content">
    <div class="row items-push">
      <div class="col-md-6 col-xl-3">
        <div class="block block-rounded text-center">
          <div class="block-content block-content-full bg-gd-emerald">
            <div class="fs-sm text-muted text-white">Program Studi</div>
            <div class="fw-semibold text-white">{{ $major->name }}</div>
          </div>
          <div class="block-content block-content-full">
            <div class="mb-3">
              <div class="fs-sm text-muted">Jenjang</div>
              <div class="fw-semibold">{{ $major->degree }} ({{ $major->formatted_degree }})</div>
            </div>
            <div class="">
              <div class="fs-sm text-muted">SKS Wajib Tempuh</div>
              <div class="fw-semibold">{{ $major->total_course_credit ?: '-' }}</div>
            </div>
          </div>
          <div class="block-content block-content-full block-content-sm bg-body-light">
            <span class="fw-semibold fs-sm text-corporate">Kode Prodi: {{ $major->code }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-9">
        @includeIf('academics.majors.elective-table')
      </div>
    </div>
  </div>
</div>

<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('Daftar Matakuliah') }}
    </h3>
  </div>
  <div class="block-content">
    <div class="mb-4">
      @can('majors.subjects.create')
      <a href="{{ route('majors.subjects.create', $major->uuid) }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.majors.subjects.create') }}
      </a>
      @endcan
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="">
          <label for="semester" class="form-label">{{ trans('Filter Berdasarkan Semester') }}</label>
          <select type="text" class="form-select" name="semester" id="semester">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach (range(1, 8) as $semester)
            <option value="{{ $semester }}">Semester {{ $semester }}</option>
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

@includeIf('academics.major_subjects.import')
@includeIf('academics.major_subjects.edit')
@endsection
@push('javascript')
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}

@vite([
'resources/js/academics/major_subjects/index.js',
'resources/js/academics/major_subjects/edit.js'
])

@endpush
