@extends('layouts.app')
@section('title', trans('page.grades.title'))
@section('hero')
<div class="content content-full">
  <h2 class="content-heading">
    {{ trans('page.grades.title') }}
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('grades.index') }}
    </nav>
  </h2>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.grades.index') }}
    </h3>
  </div>
  <div class="block-content">

    <div class="mb-4">
      @can('grades.create')
      <a href="{{ route('grades.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.grades.create') }}
      </a>
      @endcan

      @can('gardes.export')
      <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modal-select-student">
        <i class="fa fa-print fa-sm me-1"></i>
        {{ trans('Cetak Transkrip Nilai') }}
      </button>
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
@includeIf('evaluations.grades.student')
@endsection
@push('javascript')
{{ $dataTable->scripts() }}
@vite('resources/js/evaluations/grades/index.js')
<script>
  var urlDestroy = "{{ route('grades.destroy', ':uuid') }}"

</script>
@endpush
