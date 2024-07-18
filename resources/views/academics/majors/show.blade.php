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
      {{ trans('page.majors.show') }}
    </h3>
  </div>
  <div class="block-content">
    <div class="row items-push">
      <div class="col-lg-7">
        <h6 class="mb-3">{{ trans('Program Studi') }}</h6>
        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Kode') }}
            <span class="fw-semibold">{{ $major->code }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Program Studi') }}
            <span class="fw-semibold flex-grow-1 text-end">{{ $major->name }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Tingkatan') }}
            <span class="fw-semibold">{{ $major->degree }} ({{ $major->formatted_degree }})</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Total SKS Ditempuh') }}
            <span class="fw-semibold">{{ $major->total_course_credit ?: '---' }}</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="mb-4">
      @can('majors.subjects.create')
      <a href="{{ route('majors.subjects.create', $major->uuid) }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.majors.subjects.create') }}
      </a>
      @endcan
    </div>

    <div>
      <h3 class="block-title mb-3">
        {{ trans('Matakuliah di Program Studi') }} {{ $major->name }}
      </h3>
      <div class="my-3">
        {{ $dataTable->table() }}
      </div>
    </div>

  </div>
</div>
@endsection
@push('javascript')
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}

@vite('resources/js/academics/major_subjects/index.js')


@endpush