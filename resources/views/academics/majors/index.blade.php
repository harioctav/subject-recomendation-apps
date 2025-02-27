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
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.majors.index', ['majors' => trans('page.majors.title')]) }}
    </h3>
  </div>
  <div class="block-content">

    <div class="mb-4">
      @can('majors.create')
      <a href="{{ route('majors.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.majors.create', ['majors' => trans('page.majors.title')]) }}
      </a>
      @endcan
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="">
          <label for="degree" class="form-label">{{ trans('Filter Berdasarkan Jenjang Prodi') }}</label>
          <select type="text" class="form-select" name="degree" id="degree">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach (DegreeType::toArray() as $item)
            <option value="{{ $item }}">{{ ucfirst($item) }}</option>
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

@endsection
@push('javascript')
{{ $dataTable->scripts() }}
@vite('resources/js/academics/majors/index.js')
<script>
  var urlShow = "{{ route('majors.show', ':uuid') }}"
  var urlDestroy = "{{ route('majors.destroy', ':uuid') }}"

</script>
@endpush
