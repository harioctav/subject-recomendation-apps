@extends('layouts.app')
@section('title', trans('page.roles.title'))
@section('hero')
<div class="content content-full">
  <h2 class="content-heading">
    {{ trans('page.roles.title') }}
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('roles.index') }}
    </nav>
  </h2>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.roles.index', ['roles' => trans('page.roles.title')]) }}
    </h3>
  </div>
  <div class="block-content">

    @can('roles.create')
    <div class="row mb-4">
      <div class="col-md-4">
        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary">
          <i class="fa fa-plus fa-xs me-1"></i>
          {{ trans('page.roles.create', ['roles' => trans('page.roles.title')]) }}
        </a>
      </div>
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
@vite('resources/js/settings/roles/index.js')
<script>
  var urlDestroy = "{{ route('roles.destroy', ':uuid') }}"

</script>
@endpush
