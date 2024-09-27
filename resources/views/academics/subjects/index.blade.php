@extends('layouts.app')
@section('title', trans('page.subjects.title'))
@section('hero')
<div class="content content-full">
  <h2 class="content-heading">
    {{ trans('page.subjects.title') }}
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('subjects.index') }}
    </nav>
  </h2>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.subjects.index', ['subjects' => trans('page.subjects.title')]) }}
    </h3>
  </div>
  <div class="block-content">

    <div class="mb-4">
      @can('subjects.create')
      <a href="{{ route('subjects.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.subjects.create', ['subjects' => trans('page.subjects.title')]) }}
      </a>
      @endcan
    </div>

    @includeIf('components.warning-alert')

    <div class="my-3">
      {{ $dataTable->table() }}
    </div>

  </div>
</div>
@endsection
@push('javascript')
{{ $dataTable->scripts() }}
@vite('resources/js/academics/subjects/index.js')
<script>
  var urlDestroy = "{{ route('subjects.destroy', ':uuid') }}"

</script>
@endpush
