@extends('layouts.app')
@section('title') {{ trans('page.roles.title') }} @endsection
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.roles.title') }}
      <a href="{{ route('roles.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('roles.edit', $role) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.roles.edit') }}
    </h3>
  </div>
  <div class="block-content block-content-full">

    <form action="{{ route('roles.update', $role->uuid) }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf
      @method('patch')

      <div class="row">
        <div class="col-md-6">

          <div class="mb-4">
            <label class="form-label" for="name">{{ trans('Nama Peran') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="{{ trans('Nama Peran Pengguna') }}" onkeypress="return onlyLetter(event)" readonly>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <div class="space-y-2">
              <div class="form-check">
                <input type="checkbox" name="all_permission" id="all_permission" class="form-check-input @error('permission') is-invalid @enderror">
                <label for="all_permission" class="form-check-label" style="cursor: pointer">{{ trans('Pilih Semua Hak Akses') }}</label>
                @error('permission')
                <div class="invalid-feedback">
                  <strong>{{ $message }}</strong>
                </div>
                @enderror
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="row">
        @foreach ($permissions as $data)
        <div class="col-md-6">
          <div class="card push">
            <div class="card-header border-bottom-0">
              <h6 class="block-title">{{ trans('permission.' . $data->name) }}</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col">
                  @foreach ($data->permissions as $item)
                  <div class="space-y-2">
                    <div class="form-check">
                      <input class="permission form-check-input @error('permission') is-invalid @enderror" name="permission[{{ $item->name }}]" id="permission-{{ $item->name }}" type="checkbox" value="{{ $item->name }}" {{ in_array($item->name, $roleHasPermission) ? 'checked' : '' }}>
                      <label class="form-check-label" for="permission-{{ $item->name }}" style="cursor: pointer">{{ trans('permission.' . $item->name) }}</label>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="mb-3">
            <button type="submit" class="btn btn-alt-warning w-100" id="submit-button">
              <i class="fa fa-fw fa-circle-check opacity-50 me-1"></i>
              {{ trans('button.edit') }}
            </button>
          </div>
        </div>
      </div>

    </form>

  </div>
</div>
@endsection
@push('javascript')
<script>
  window.translations = @json(trans('permission'));

</script>
@vite('resources/js/settings/roles/input.js')
@endpush
