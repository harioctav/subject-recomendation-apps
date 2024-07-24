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
      {{ trans('page.grades.create') }} Step Ketiga
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ route('grades.store') }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf

      <input type="hidden" name="step" value="3">

      <div class="row justify-content-center">
        <div class="col-md-6">

          <div class="mb-4">
            <label for="subject_id" class="form-label">{{ trans('Matakuliah') }}</label>
            <span class="text-danger">*</span>
            <select name="subject_id" id="subject_id" class="js-select2 form-select @error('subject_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Matakuliah') }}" style="width: 100%;" data-old="{{ old('subject_id') }}">
              <option></option>
              @foreach ($subjects as $item)
              <option value="{{ $item->id }}" @if (old('subject_id')==$item->id) selected @endif>{{ $item->name }}</option>
              @endforeach
            </select>
            @error('subject_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="grade" class="form-label">{{ trans('Nilai') }}</label>
            <span class="text-danger">*</span>
            <input type="number" min="1" step="1" name="grade" id="grade" value="{{ old('grade') }}" class="form-control @error('grade') is-invalid @enderror" placeholder="{{ trans('Masukkan Nilai') }}" onkeypress="return onlyNumber(event)">
            @error('grade')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <button type="submit" class="btn btn-alt-primary w-100" id="submit-button">
              <i class="fa fa-fw fa-circle-check me-1"></i>
              {{ trans('button.create') }}
            </button>
          </div>

        </div>
      </div>

    </form>
  </div>
</div>
@endsection
