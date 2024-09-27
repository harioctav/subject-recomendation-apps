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
      {{ Breadcrumbs::render('subjects.show', $subject) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.subjects.show', ['subjects' => trans('page.subjects.title')]) }}
    </h3>
  </div>
  <div class="block-content">
    <div class="row items-push">
      <div class="col-lg-3">
        @include('components.subject-notes')
      </div>
      <div class="col-lg-7 offset-lg-1">
        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Kode Matakuliah') }}
            <span class="fw-semibold">{{ $subject->code }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Nama Matakuliah') }}
            <span class="fw-semibold">{{ $subject->name }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Status Matakuliah') }}
            <span class="fw-semibold">{{ $subject->status }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Jumlah SKS') }}
            <span class="fw-semibold">{{ $subject->course_credit }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Catatan') }}
            <span class="fw-semibold">{{ $subject->note ?: '---' }}</span>
          </li>
        </ul>
        <a href="{{ route('subjects.index') }}" class="btn btn-danger btn-sm">
          <i class="fa fa-sm fa-arrow-left me-2"></i>
          {{ trans('button.back') }}
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
