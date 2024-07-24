@extends('layouts.app')
@section('title', trans('page.grades.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.grades.title') }}
      <a href="{{ route('grades.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('grades.create') }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.grades.create') }} Step Pertama
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ route('grades.store') }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf

      <input type="hidden" name="step" value="1">

      <div class="row justify-content-center">
        <div class="col-md-6">

          <div class="mb-4">
            <label for="major_id" class="form-label">{{ trans('Program Studi') }}</label>
            <span class="text-danger">*</span>
            <select name="major_id" id="major_id" class="js-select2 form-select @error('major_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Program Studi') }}" style="width: 100%;" data-old="{{ old('major_id') }}">
              <option></option>
              @foreach ($majors as $item)
              <option value="{{ $item->id }}" @if (old('major_id')==$item->id) selected @endif>{{ $item->name }}</option>
              @endforeach
            </select>
            @error('major_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <button type="submit" class="btn btn-alt-primary w-100" id="submit-button">
              <i class="fa fa-fw fa-circle-check me-1"></i>
              {{ trans('Selanjutnya') }}
            </button>
          </div>

        </div>
      </div>

    </form>
  </div>
</div>
@endsection
