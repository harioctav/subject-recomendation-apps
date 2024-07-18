@extends('layouts.app')
@section('title', trans('page.majors.title'))
@section('hero')
<div class="content content-full">
  <h2 class="content-heading">
    {{ trans('page.majors.title') }}
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('majors.index') }}
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
      {{ trans('page.majors.index') }}
    </h3>
  </div>
  <div class="block-content">

    <div class="mb-4">
      @can('majors.create')
      <a href="{{ route('majors.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.majors.create') }}
      </a>
      @endcan
      @can('majors.import')
      <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modal-fadein">{{ trans('Import Data Jurusan') }}</button>
      @endcan
    </div>

    <div class="my-3">
      {{ $dataTable->table() }}
    </div>

  </div>
</div>

@includeIf('academics.majors.import')
@endsection
@push('javascript')
{{ $dataTable->scripts() }}
@vite('resources/js/academics/majors/index.js')
<script>
  var urlShow = "{{ route('majors.show', ':uuid') }}"
  var urlDestroy = "{{ route('majors.destroy', ':uuid') }}"

</script>
@endpush
