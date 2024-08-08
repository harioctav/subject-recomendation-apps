@extends('layouts.app')
@section('title', trans('page.recommendations.title'))
@section('hero')
<div class="content content-full">
  <h2 class="content-heading">
    {{ trans('page.recommendations.title') }}
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('recommendations.index') }}
    </nav>
  </h2>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.recommendations.index') }}
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
@vite('resources/js/evaluations/recommendations/index.js')
{{-- <script>
  var urlDestroy = "{{ route('recommendations.destroy', ':uuid') }}"

</script> --}}
@endpush
