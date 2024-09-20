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

    <div class="row">
      <div class="col-md-4">
        <div class="">
          <label for="status" class="form-label">{{ trans('Filter Berdasarkan Status Mahasiswa') }}</label>
          <select type="text" class="form-select" name="status" id="status">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach (StudentStatus::toArray() as $item)
            <option value="{{ $item }}">{{ ucfirst($item) }}</option>
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
@vite('resources/js/evaluations/recommendations/index.js')
@endpush
