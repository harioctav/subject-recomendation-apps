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

    @can('grades.create')
    <div class="mb-4">
      <a href="{{ route('grades.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.grades.create') }}
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
@vite('resources/js/evaluations/grades/index.js')
<script>
  var urlDestroy = "{{ route('grades.destroy', ':uuid') }}"

</script>
@endpush
