@extends('layouts.app')
@section('title', trans('page.students.title'))
@section('hero')
<div class="content content-full">
  <h2 class="content-heading">
    {{ trans('page.students.title') }}
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('students.index') }}
    </nav>
  </h2>
</div>
@endsection
@section('content')
<div id="loading-animation" style="display:none;">
  <div class="loading-spinner"></div>
  <p>Loading...</p>
</div>

<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.students.index') }}
    </h3>
    <div class="block-options d-flex flex-column flex-sm-row justify-content-between align-items-center">
      <div class="mb-1 mb-sm-0">
        <div class="space-y-2">
          <div class="form-check form-switch">
            <input class="form-check-input me-2" type="checkbox" value="false" id="isTrash-switch" name="isTrash">
            <label class="form-check-label" style="cursor: pointer" for="isTrash-switch">
              {{ trans('button.recycle') }}
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="block-content">

    @can('students.create')
    <div class="mb-4">
      <a href="{{ route('students.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.students.create') }}
      </a>

      @can('students.import')
      <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modal-fadein">
        <i class="fa fa-file-excel fa-sm me-1"></i>
        {{ trans('button.import', ['import' => trans('page.students.title')]) }}
      </button>
      @endcan
    </div>
    @endcan

    <div class="row">
      <div class="col-md-4">
        <div class="">
          <label for="status" class="form-label">{{ trans('Filter Berdasarkan Status Mahasiswa') }}</label>
          <select type="text" class="form-select" name="status" id="status">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach ($status as $item)
            <option value="{{ $item }}">{{ ucfirst($item) }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="">
          <label for="student-status" class="form-label">{{ trans('Filter Berdasarkan Status Keaktifan') }}</label>
          <select type="text" class="form-select" name="student_status" id="student-status">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach (AccountStatus::toArray() as $item)
            <option value="{{ $item }}">{{ $item ? ucfirst('Aktif') : ucfirst('Tidak Aktif') }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    @includeIf('components.warning-alert')

    <div class="my-3">
      {{ $dataTable->table() }}
    </div>

  </div>
</div>
@includeIf('academics.students.import')
@endsection
@push('javascript')
{{ $dataTable->scripts() }}
@vite('resources/js/academics/students/index.js')
<script>
  var urlDestroy = "{{ route('students.destroy', ':uuid') }}"
  var urlRestore = "{{ route('students.restore', ':uuid') }}";
  var urlForceDelete = "{{ route('students.delete', ':uuid') }}";

</script>
@endpush
