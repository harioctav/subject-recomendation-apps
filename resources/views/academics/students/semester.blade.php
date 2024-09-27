@extends('layouts.app')
@section('title', trans('page.students.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.students.title') }}
      <a href="{{ route('students.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('students.show', $student) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.students.show', ['students' => trans('page.students.title')]) }}
    </h3>
  </div>
  <div class="block-content">

    {{-- Student Info --}}
    @includeIf('components.students.info')
    {{-- Student Info --}}

  </div>
</div>
@endsection
