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
      {{ Breadcrumbs::render('recommendations.show', $student) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.recommendations.show') }}
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

        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Program Studi') }}
            <span class="fw-semibold text-end">{{ $student->major->name }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Status Mahasiswa') }}
            <span class="fw-semibold text-end">{{ $student->status }}</span>
          </li>
        </ul>

      </div>
      <div class="col-lg-6 offset-lg-1">
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
            <span class="fw-semibold">{{ $data['gpa'] }}</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="mb-2">
      @can('grades.show')
      <a href="{{ route('grades.show', $student) }}" class="btn btn-sm btn-primary">
        <i class="fa fa-pencil-alt fa-sm me-1"></i>
        {{ trans('Input Nilai') }}
      </a>
      @endcan

      @can('recommendations.create')
      <a href="{{ route('recommendations.create', $student) }}" class="btn btn-sm btn-secondary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('Input Rekomendasi') }}
      </a>
      @endcan

      @can('recommendations.export')
      <a href="{{ route('recommendations.export', $student) }}" target="_blank" class="btn btn-sm btn-success">
        <i class="fa fa-print fa-sm me-1"></i>
        {{ trans('Cetak Hasil Rekomendasi') }}
      </a>
      @endcan
    </div>

    <div class="my-3">
      {{ $dataTable->table() }}
    </div>

  </div>
</div>
@endsection
@push('javascript')
{{ $dataTable->scripts() }}
@vite('resources/js/evaluations/recommendations/show.js')
<script>
  var urlDestroy = "{{ route('recommendations.destroy', ':uuid') }}"

</script>
@endpush
