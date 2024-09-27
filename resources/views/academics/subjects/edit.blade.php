@extends('layouts.app')
@section('title', trans('page.subjects.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.subjects.title') }}
      <a href="{{ route('subjects.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('subjects.edit', $subject) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.subjects.edit', ['subjects' => trans('page.subjects.title')]) }}
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ route('subjects.update', $subject->uuid) }}" method="POST" onsubmit="return disableSubmitButton()">
      @csrf
      @method('PATCH')

      <div class="row items-push">
        <div class="col-lg-3">
          @include('components.subject-notes')
        </div>
        <div class="col-lg-7 offset-lg-1">

          <div class="mb-4">
            <label for="code" class="form-label">{{ trans('Kode') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="code" id="code" value="{{ old('code', $subject->code) }}" class="form-control @error('code') is-invalid @enderror" placeholder="{{ trans('Kode Matakuliah') }}">
            @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="name" class="form-label">{{ trans('Matakuliah') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="name" id="name" value="{{ old('name', $subject->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="{{ trans('Nama Matakuliah') }}" onkeypress="return onlyLetter(event)">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="course_credit" class="form-label">{{ trans('SKS') }}</label>
            <span class="text-danger">*</span>
            <input type="number" min="1" max="5" step="1" name="course_credit" id="course_credit" value="{{ old('course_credit', $subject->course_credit) }}" class="form-control @error('course_credit') is-invalid @enderror" placeholder="{{ trans('Jumlah SKS') }}">
            @error('course_credit')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="exam_time" class="form-label">{{ trans('Waktu Ujian') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="exam_time" id="exam_time" value="{{ old('exam_time', $subject->exam_time) }}" class="form-control @error('exam_time') is-invalid @enderror" placeholder="{{ trans('Waktu Ujian') }}">
            @error('exam_time')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="status" class="form-label">{{ trans('Status') }}</label>
            <span class="text-danger">*</span>
            <select name="status" id="status" class="js-select2 form-select @error('status') is-invalid @enderror" data-placeholder="{{ trans('Status Matakuliah') }}" style="width: 100%;">
              <option></option>
              @foreach ($status as $value)
              <option value="{{ $value }}" @if (old('status', $subject->status)==$value) selected @endif>{{ $value }}</option>
              @endforeach
            </select>
            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label">{{ trans('Keterangan') }}</label>
            <div class="space-x">
              @php
              $subjectNotes = old('notes', $subject->note ? explode(' | ', $subject->note) : []);
              $subjectNotes = array_map('trim', $subjectNotes);
              @endphp
              @foreach ($notes as $index => $note)
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" value="{{ $note }}" id="checkbox-{{ $index }}" name="notes[]" @if(in_array($note, $subjectNotes)) checked @endif>
                <label class="form-check-label" for="checkbox-{{ $index }}">{{ $note }}</label>
              </div>
              @endforeach
            </div>
          </div>

          <div class="mb-4">
            <button type="submit" class="btn btn-alt-warning w-100" id="submit-button">
              <i class="fa fa-fw fa-circle-check me-1"></i>
              {{ trans('button.edit') }}
            </button>
          </div>

        </div>
      </div>

    </form>
  </div>
</div>
@endsection
