@extends('layouts.app')
@section('title', trans('page.users.title'))
@section('hero')
<div class="content content-full">
  <h2 class="content-heading">
    {{ trans('page.users.title') }}
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('users.index') }}
    </nav>
  </h2>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.users.index', [
        'users' => trans('page.users.title')
      ]) }}
    </h3>
  </div>
  <div class="block-content">

    @can('users.create')
    <div class="mb-4">
      <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.users.create', [
          'users' => trans('page.users.title')
        ]) }}
      </a>
    </div>
    @endcan

    <div class="row">
      <div class="col-md-4">
        <div class="">
          <label for="status" class="form-label">{{ trans('Filter Berdasarkan Status Akun') }}</label>
          <select type="text" class="form-select" name="status" id="status">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach ($status as $item)
            <option value="{{ $item }}">{{ $item ? ucfirst('Aktif') : ucfirst('Tidak Aktif') }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="">
          <label for="roles" class="form-label">{{ trans('Filter Berdasarkan Peran Pengguna') }}</label>
          <select type="text" class="form-select" name="roles" id="roles">
            <option value="{{ Helper::ALL }}">{{ Helper::ALL }}</option>
            @foreach ($roleTypes as $item)
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
@vite('resources/js/settings/users/index.js')
<script>
  var urlStatus = "{{ route('users.status', ':uuid') }}"
  var urlDestroy = "{{ route('users.destroy', ':uuid') }}"

</script>
@endpush
