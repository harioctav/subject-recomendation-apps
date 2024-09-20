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
      {{ trans('page.students.show') }}
    </h3>
  </div>
  <div class="block-content">

    <div class="row items-push">
      <div class="col-md-6 col-xl-3">
        <div class="block block-rounded text-center my-0">
          <div class="block-content block-content-full bg-gd-dusk">
            <img class="img-avatar img-avatar-thumb" src="{{ $student->getAvatar() }}" alt="{{ trans('Foto Avatar') }}">
          </div>
          <div class="block-content block-content-full">
            <div class="fw-semibold mb-1">{{ $student->name }}</div>
            <div class="fs-sm text-muted">{{ $student->nim }}</div>
          </div>
          <div class="block-content block-content-full block-content-sm bg-body-light mb-3">
            <span class="fw-semibold fs-sm text-elegance">{{ $student->email ?: '--' }}</span>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-auto mb-1">
            @can('students.semester')
            <a href="{{ route('students.semester') }}" class="btn btn-sm btn-secondary text-center" onclick="event.preventDefault(); document.getElementById('nim-form').submit();">
              <i class="fa fa-file-excel fa-sm me-2"></i>
              {{ trans('Cek Data Sisa Semester') }}
            </a>

            <form id="nim-form" action="{{ route('students.semester') }}" method="POST" class="d-none">
              @csrf
              <input type="text" class="d-none" value="{{ $student->nim }}" name="nim">
            </form>
            @endcan
          </div>
          <div class="col-md-auto">
            @can('students.data')
            <a href="{{ route('students.data') }}" class="btn btn-sm btn-primary text-center" onclick="event.preventDefault(); document.getElementById('nim-form-data').submit();">
              <i class="fa fa-file-excel fa-sm me-2"></i>
              {{ trans('Cek Data Nilai Matakuliah') }}
            </a>

            <form id="nim-form-data" action="{{ route('students.data') }}" method="POST" class="d-none">
              @csrf
              <input type="text" class="d-none" value="{{ $student->nim }}" name="nim">
            </form>
            @endcan
          </div>
        </div>

      </div>
      <div class="col-md-6 col-xl-9">
        <div class="py-1 my-0 mb-1">
          <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
            {{ trans('Informasi Umum') }}
          </p>
        </div>
        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('UPBJJ') }}
            <span class="fw-semibold">{{ $student->upbjj ?: '--' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Program Studi') }}
            <span class="fw-semibold">{{ $student->major->name }} - {{ $student->major->formatted_degree }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Registrasi Awal') }}
            <span class="fw-semibold">{{ $student->initial_registration_period ?: "--" }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Jenis Kelamin') }}
            <span class="fw-semibold">{{ ucfirst(trans($student->gender)) }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Tempat Lahir') }}
            <span class="fw-semibold">{{ $student->birth_place ?: '--' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Tanggal Lahir') }}
            <span class="fw-semibold">{{ $student->formatted_birth_date }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Usia') }}
            <span class="fw-semibold">{{ $student->age }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Agama') }}
            <span class="fw-semibold">{{ ucfirst($student->religion) ?: "--" }}</span>
          </li>
        </ul>

        <div class="py-1 my-0 mb-1">
          <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
            {{ trans('Informasi Kontak') }}
          </p>
        </div>
        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('No. Handphone') }}
            <span class="fw-semibold">{{ $student->phone ?: '--' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Email Pribadi') }}
            <span class="fw-semibold">{{ $student->email ?: "--" }}</span>
          </li>
        </ul>

        <div class="py-1 my-0 mb-1">
          <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
            {{ trans('Informasi Orang Tua') }}
          </p>
        </div>
        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Nama Ibu Kandung') }}
            <span class="fw-semibold">{{ $student->parent_name ?: "--" }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('No. Handphone') }}
            <span class="fw-semibold">{{ $student->parent_phone_number ?: "--" }}</span>
          </li>
        </ul>

        <div class="py-1 my-0 mb-1">
          <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
            {{ trans('Informasi Alamat') }}
          </p>
        </div>
        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Provinsi') }}
            <span class="fw-semibold">{{ $student->province->name ?: '--' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Kabupaten/Kota') }}
            <span class="fw-semibold">{{ $student->regency->name ?: '--'}}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Kecamatan') }}
            <span class="fw-semibold">{{ $student->district->name ?: '--' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Desa/Kelurahan') }}
            <span class="fw-semibold">{{ optional($student->village)->name ?: '--' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Kode Pos') }}
            <span class="fw-semibold">{{ optional($student->village)->pos_code ?: '--' }}</span>
          </li>
          <li class="list-group-item">
            {{ trans('Alamat') }}
            <p class="fw-semibold mb-0 mt-1" style="text-align: justify">{{ $student->address ?: '--' }}</p>
          </li>
        </ul>

        <a href="{{ route('students.index') }}" class="btn btn-danger btn-sm">
          <i class="fa fa-sm fa-arrow-left me-2"></i>
          {{ trans('button.back') }}
        </a>

      </div>
    </div>

  </div>
</div>
@endsection
