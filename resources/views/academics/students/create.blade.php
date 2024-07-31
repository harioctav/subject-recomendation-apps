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
      {{ Breadcrumbs::render('students.create') }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.students.create') }}
    </h3>
  </div>
  <div class="block-content">

    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return disableSubmitButton()">
      @csrf

      <div class="row items-push">
        <div class="col-lg-3">
          <span class="text-muted">
            Isian wajib di isi untuk kolom yang memiliki tanda <span class="text-danger">*</span> (bintang). Dan untuk yang tidak memiliki tanda <span class="text-danger">*</span> (bintang), boleh dikosongkan.
          </span>
        </div>
        <div class="col-lg-7 offset-1">

          <div class="py-1 mb-1">
            <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
              {{ trans('Informasi Data Pribadi') }}
            </p>
          </div>

          <div class="mb-4">
            <label for="nim" class="form-label">{{ trans('NIM') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="nim" id="nim" value="{{ old('nim') }}" class="form-control @error('nim') is-invalid @enderror" placeholder="{{ trans('Masukkan Nomor Induk Mahasiswa') }}" onkeypress="return onlyNumber(event)">
            @error('nim')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="nik" class="form-label">{{ trans('NIK') }}</label>
            <input type="text" name="nik" id="nik" value="{{ old('nik') }}" class="form-control @error('nik') is-invalid @enderror" placeholder="{{ trans('Masukkan Nomor Induk Kependudukan') }}" onkeypress="return onlyNumber(event)">
            @error('nik')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="name" class="form-label">{{ trans('Nama Lengkap') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="{{ trans('Masukkan Nama Lengkap') }}" onkeypress="return onlyLetter(event)">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="email" class="form-label">{{ trans('Email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="{{ trans('Masukkan Email Aktif') }}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <div class="row">
              <div class="col-md-8">
                <label for="birth_place" class="form-label">{{ trans('Tempat Lahir') }}</label>
                <span class="text-danger">*</span>
                <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place') }}" class="form-control @error('birth_place') is-invalid @enderror" placeholder="{{ trans('Masukkan Tempat Lahir') }}" onkeypress="return onlyLetter(event)">
                @error('birth_place')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4">
                <div class="mb-1">
                  <label class="form-label" for="birth_date">{{ trans('Tanggal Lahir') }}</label>
                  <span class="text-danger">*</span>
                  <input type="text" class="js-flatpickr form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" placeholder="Y-m-d" data-date-format="Y-m-d">
                  @error('birth_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
          </div>

          <div class="mb-4">
            <label for="gender" class="form-label">{{ trans('Jenis Kelamin') }}</label>
            <span class="text-danger">*</span>
            <select name="gender" id="gender" class="js-select2 form-select @error('gender') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;">
              <option></option>
              @foreach ($genders as $value)
              <option value="{{ $value }}" @if (old('gender')==$value) selected @endif>{{ ucfirst(trans($value)) }}</option>
              @endforeach
            </select>
            @error('gender')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="religion" class="form-label">{{ trans('Agama') }}</label>
            <select name="religion" id="religion" class="js-select2 form-select @error('religion') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;">
              <option></option>
              @foreach ($religions as $value)
              <option value="{{ $value }}" @if (old('religion')==$value) selected @endif>{{ ucfirst($value) }}</option>
              @endforeach
            </select>
            @error('religion')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="phone" class="form-label">{{ trans('No. Whatsapp') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="{{ trans('Masukkan Nomor Aktif') }}" onkeypress="return onlyNumber(event)">
            @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="row mb-0">
            <div class="col-md-10 col-xl-8">
              <div class="push">
                <img class="img-prev img-avatar" src="{{ asset('assets/images/placeholders/default-avatar.png') }}" alt="">
              </div>
              <div class="mb-4">
                <label class="form-label" for="image">{{ trans('Pas Foto Mahasiswa') }}</label>
                <input class="form-control @error('file') is-invalid @enderror" type="file" accept="image/*" id="image" name="file" onchange="return previewImage()">
                @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <div class="mb-4">
            <label for="major_id" class="form-label">{{ trans('Program Studi') }}</label>
            <span class="text-danger">*</span>
            <select name="major_id" id="major_id" class="js-select2 form-select @error('major_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Program Studi') }}" style="width: 100%;">
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
            <label for="initial_registration_period" class="form-label">{{ trans('Registrasi Awal') }}</label>
            <input type="text" name="initial_registration_period" id="initial_registration_period" value="{{ old('initial_registration_period') }}" class="form-control @error('initial_registration_period') is-invalid @enderror" placeholder="{{ trans('Masukkan Registrasi Awal') }}">
            @error('initial_registration_period')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="origin_department" class="form-label">{{ trans('Jurusan Asal') }}</label>
            <input type="text" name="origin_department" id="origin_department" value="{{ old('origin_department') }}" class="form-control @error('origin_department') is-invalid @enderror" placeholder="{{ trans('Masukkan Jurusan Asal') }}">
            @error('origin_department')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="status" class="form-label">{{ trans('Status Mahasiswa') }}</label>
            <span class="text-danger">*</span>
            <select name="status" id="status" class="js-select2 form-select @error('status') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;">
              <option></option>
              @foreach ($status as $value)
              <option value="{{ $value }}" @if (old('status')==$value) selected @endif>{{ ucfirst(trans($value)) }}</option>
              @endforeach
            </select>
            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="upbjj" class="form-label">{{ trans('UPBJJ') }}</label>
            <input type="text" name="upbjj" id="upbjj" value="{{ old('upbjj') }}" class="form-control @error('upbjj') is-invalid @enderror" placeholder="{{ trans('Masukkan UPBJJ') }}">
            @error('upbjj')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="py-1 mb-1">
            <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
              {{ trans('Informasi Tempat Tinggal') }}
            </p>
          </div>

          <div class="mb-4">
            <label for="province" class="form-label">{{ trans('Pilih Provinsi') }}</label>
            <span class="text-danger">*</span>
            <select name="province" id="province" class="js-select2 form-select @error('province') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('province') }}">
              <option></option>
              @foreach ($provinces as $item)
              <option value="{{ $item->id }}" @if (old('province')==$item->id) selected @endif>{{ $item->name }}</option>
              @endforeach
            </select>
            @error('province')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="regency" class="form-label">{{ trans('Pilih Kabupaten') }}</label>
            <span class="text-danger">*</span>
            <select name="regency" id="regency" class="js-select2 form-select @error('regency') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('regency') }}">
              <option></option>

            </select>
            @error('regency')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="district" class="form-label">{{ trans('Pilih Kecamatan') }}</label>
            <span class="text-danger">*</span>
            <select name="district" id="district" class="js-select2 form-select @error('district') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('district') }}">
              <option></option>

            </select>
            @error('district')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="village" class="form-label">{{ trans('Pilih Kelurahan') }}</label>
            <span class="text-danger">*</span>
            <select name="village" id="village" class="js-select2 form-select @error('village') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('village') }}">
              <option></option>

            </select>
            @error('village')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="post_code" class="form-label">{{ trans('Kode Pos') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="post_code" id="post_code" value="{{ old('post_code') }}" class="form-control @error('post_code') is-invalid @enderror" placeholder="{{ trans('Masukkan Kode Pos') }}" onkeypress="return onlyNumber(event)" data-old="{{ old('post_code') }}" readonly>
            @error('post_code')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label" for="address">{{ trans('Alamat Lengkap') }}</label>
            <span class="text-danger">*</span>
            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="4" placeholder="{{ trans('Masukkan Alamat lengkap') }}">{{ old('address') }}</textarea>
            @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="py-1 mb-1">
            <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
              {{ trans('Informasi Tambahan') }}
            </p>
          </div>

          <div class="mb-4">
            <label for="parent_name" class="form-label">{{ trans('Nama Ibu Kandung') }}</label>
            <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name') }}" class="form-control @error('parent_name') is-invalid @enderror" placeholder="{{ trans('Masukkan Nama Ibu Kandung') }}" onkeypress="return onlyLetter(event)">
            @error('parent_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="parent_phone_number" class="form-label">{{ trans('No. Orang Tua') }}</label>
            <input type="text" name="parent_phone_number" id="parent_phone_number" value="{{ old('parent_phone_number') }}" class="form-control @error('parent_phone_number') is-invalid @enderror" placeholder="{{ trans('Masukkan Nomor Aktif') }}" onkeypress="return onlyNumber(event)">
            @error('parent_phone_number')
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
@push('javascript')
@vite('resources/js/academics/students/input.js')

<script>
  var regencies_url = "{{ route('locations.regencies', ':province_id') }}"
  var districts_url = "{{ route('locations.districts', ':regency_id') }}"
  var villages_url = "{{ route('locations.villages', ':district_id') }}"
  var pos_code_url = "{{ route('locations.postCodes', ':village_id') }}"

</script>
@endpush
