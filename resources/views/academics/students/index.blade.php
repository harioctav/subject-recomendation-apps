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

    <div class="row">
      <div class="col-md-4">
        <div class="mb-4">
          <label for="status" class="form-label">{{ trans('Filter Berdasarkan Status Mahasiswa') }}</label>
          <select type="text" class="form-select" name="status" id="status">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach ($status as $item)
            <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    @can('students.create')
    <div class="mb-0">
      <a href="{{ route('students.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.students.create') }}
      </a>
    </div>
    @endcan

    <div class="my-3">
      {{ $dataTable->table() }}
    </div>

  </div>
</div>
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
