@extends('layouts.app')
@section('title', trans('page.majors.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.majors.title') }}
      <a href="{{ route('majors.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('majors.show', $major) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.majors.show', ['majors' => trans('page.majors.title')]) }}
    </h3>
  </div>
  <div class="block-content">
    <div class="row items-push">
      <div class="col-lg-7">
        <h6 class="mb-3">{{ trans('Program Studi') }}</h6>
        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Kode') }}
            <span class="fw-semibold">{{ $major->code }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Program Studi') }}
            <span class="fw-semibold flex-grow-1 text-end">{{ $major->name }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Tingkatan') }}
            <span class="fw-semibold">{{ $major->degree }} ({{ $major->formatted_degree }})</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Total SKS Yang WAJIB Ditempuh') }}
            <span class="fw-semibold">{{ $major->total_course_credit ?: '---' }}</span>
          </li>
        </ul>
      </div>
      <div class="col-lg-5">
        <h6 class="mb-3">{{ trans('Matakuliah Pilihan') }}</h6>
        <div class="table-responsive text-center">
          <table class="table table-striped table-vcenter table-bordered">
            <thead>
              <tr>
                <th>Semester</th>
                <th>Jumlah</th>
              </tr>
            </thead>
            <tbody>
              @foreach($major->getElectiveSubjectsBySemester() as $elective)
              <tr>
                <td>Semester {{ $elective['semester'] }}</td>
                <td>{{ $elective['count'] }} Matakuliah</td>
              </tr>
              @endforeach

              @forelse ($major->getElectiveSubjectsBySemester() as $elective)
              <tr>
                <td>Semester {{ $elective['semester'] }}</td>
                <td>{{ $elective['count'] }} Matakuliah</td>
              </tr>
              @empty
              <tr>
                <td colspan="2">Data Kosong</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('Daftar Matakuliah') }}
    </h3>
  </div>
  <div class="block-content">
    <div class="mb-4">
      @can('majors.subjects.create')
      <a href="{{ route('majors.subjects.create', $major->uuid) }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus fa-sm me-1"></i>
        {{ trans('page.majors.subjects.create') }}
      </a>
      @endcan
    </div>

    <div class="my-3">
      {{ $dataTable->table() }}
    </div>
  </div>
</div>

@includeIf('academics.major_subjects.import')
@endsection
@push('javascript')
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}

@vite('resources/js/academics/major_subjects/index.js')


@endpush
