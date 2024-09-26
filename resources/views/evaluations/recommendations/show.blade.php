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

    {{-- Student Info --}}
    @includeIf('components.students.info')
    {{-- Student Info --}}

    <div class="row">
      <div class="col-md-auto">
        <div class="mb-3">
          @can('grades.show')
          <a href="{{ route('grades.show', $student) }}" class="btn btn-sm btn-primary">
            <i class="fa fa-pencil-alt fa-sm me-1"></i>
            {{ trans('Input Data Nilai Matakuliah') }}
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
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="">
          <label for="note" class="form-label">{{ trans('Filter Berdasarkan Catatan') }}</label>
          <select type="text" class="form-select" name="note" id="note">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach (RecommendationStatus::toArray() as $item)
            <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
          </select>
        </div>
      </div>
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
@endsection
@push('javascript')
{{ $dataTable->scripts() }}
@vite('resources/js/evaluations/recommendations/show.js')
<script>
  var urlDestroy = "{{ route('recommendations.destroy', ':uuid') }}"

</script>
@endpush
