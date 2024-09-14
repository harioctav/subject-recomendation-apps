@extends('layouts.app')
@section('title', trans('page.activities.title'))
@section('hero')
<div class="content content-full">
  <h2 class="content-heading">
    {{ trans('page.activities.title') }}
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('activities.index') }}
    </nav>
  </h2>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.activities.index') }}
    </h3>
  </div>
  <div class="block-content">

    <div class="my-3">
      {{ $dataTable->table() }}
    </div>

  </div>
</div>
@endsection
@push('javascript')
{{ $dataTable->scripts() }}
<script>
</script>
@endpush
