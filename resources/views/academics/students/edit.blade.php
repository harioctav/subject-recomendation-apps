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
      {{ Breadcrumbs::render('students.edit', $student) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.students.edit') }}
    </h3>
  </div>
  <div class="block-content">
    <form action="{{ route('students.update', $student->uuid) }}" method="POST" enctype="multipart/form-data" onsubmit="return disableSubmitButton()">
      @csrf
      @method('PATCH')

      @error('province')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror

      <div class="row items-push justify-content-center">
        <div class="col-md-6">

          <div class="mb-4">
            <label for="nim" class="form-label">{{ trans('NIM') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="nim" id="nim" value="{{ old('nim', $student->nim) }}" class="form-control @error('nim') is-invalid @enderror" placeholder="{{ trans('Masukkan Nomor Induk Mahasiswa') }}" onkeypress="return onlyNumber(event)">
            @error('nim')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="nik" class="form-label">{{ trans('NIK') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="nik" id="nik" value="{{ old('nik', $student->nik) }}" class="form-control @error('nik') is-invalid @enderror" placeholder="{{ trans('Masukkan Nomor Induk Kependudukan') }}" onkeypress="return onlyNumber(event)">
            @error('nik')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="name" class="form-label">{{ trans('Nama Lengkap') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="{{ trans('Masukkan Nama Lengkap') }}" onkeypress="return onlyLetter(event)">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="email" class="form-label">{{ trans('Email') }}</label>
            <span class="text-danger">*</span>
            <input type="email" name="email" id="email" value="{{ old('email', $student->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="{{ trans('Masukkan Email Aktif') }}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <div class="row">
              <div class="col-md-8">
                <label for="birth_place" class="form-label">{{ trans('Tempat Lahir') }}</label>
                <span class="text-danger">*</span>
                <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $student->birth_place) }}" class="form-control @error('birth_place') is-invalid @enderror" placeholder="{{ trans('Masukkan Tempat Lahir') }}" onkeypress="return onlyLetter(event)">
                @error('birth_place')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4">
                <div class="mb-1">
                  <label class="form-label" for="birth_date">{{ trans('Tanggal Lahir') }}</label>
                  <span class="text-danger">*</span>
                  <input type="text" class="js-flatpickr form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date) }}" placeholder="Y-m-d" data-date-format="Y-m-d">
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
              <option value="{{ $value }}" @if (old('gender', $student->gender)==$value) selected @endif>{{ ucfirst(trans($value)) }}</option>
              @endforeach
            </select>
            @error('gender')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="religion" class="form-label">{{ trans('Agama') }}</label>
            <span class="text-danger">*</span>
            <select name="religion" id="religion" class="js-select2 form-select @error('religion') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;">
              <option></option>
              @foreach ($religions as $value)
              <option value="{{ $value }}" @if (old('religion', $student->religion)==$value) selected @endif>{{ ucfirst($value) }}</option>
              @endforeach
            </select>
            @error('religion')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="phone" class="form-label">{{ trans('No. Whatsapp') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $student->phone) }}" class="form-control @error('phone') is-invalid @enderror" placeholder="{{ trans('Masukkan Nomor Aktif') }}" onkeypress="return onlyNumber(event)">
            @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="row mb-0">
            <div class="col-md-10 col-xl-8">
              <div class="push">
                <img class="img-prev img-avatar" src="{{ $student->getAvatar() }}" alt="Foro Mahasiswa">
              </div>
              <div class="mb-4">
                <label class="form-label" for="image">{{ trans('Pas Foto Baru Mahasiswa') }}</label>
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
              <option value="{{ $item->id }}" @if (old('major_id', $student->major_id)==$item->id) selected @endif>{{ $item->name }}</option>
              @endforeach
            </select>
            @error('major_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="initial_registration_period" class="form-label">{{ trans('Tahun Masuk') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="initial_registration_period" id="initial_registration_period" value="{{ old('initial_registration_period', $student->initial_registration_period) }}" class="form-control @error('initial_registration_period') is-invalid @enderror" placeholder="{{ trans('Masukkan Tahun Masuk') }}" onkeypress="return onlyNumber(event)">
            @error('initial_registration_period')
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
            <select name="province" id="province" class="js-select2 form-select @error('province') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('province', $student->village->district->regency->province->id) }}">
              <option></option>
              @foreach ($provinces as $item)
              <option value="{{ $item->id }}" @if (old('province', $student->village->district->regency->province->id)==$item->id) selected @endif>{{ $item->name }}</option>
              @endforeach
            </select>
            @error('province')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="regency" class="form-label">{{ trans('Pilih Kabupaten') }}</label>
            <span class="text-danger">*</span>
            <select name="regency" id="regency" class="js-select2 form-select @error('regency') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('regency', $student->village->district->regency->id) }}">
              <option></option>

            </select>
            @error('regency')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="district" class="form-label">{{ trans('Pilih Kecamatan') }}</label>
            <span class="text-danger">*</span>
            <select name="district" id="district" class="js-select2 form-select @error('district') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('district', $student->village->district->id) }}">
              <option></option>

            </select>
            @error('district')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="village" class="form-label">{{ trans('Pilih Kelurahan') }}</label>
            <span class="text-danger">*</span>
            <select name="village" id="village" class="js-select2 form-select @error('village') is-invalid @enderror" data-placeholder="{{ trans('Pilih Salah Satu') }}" style="width: 100%;" data-old="{{ old('village', $student->village->id) }}">
              <option></option>

            </select>
            @error('village')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="post_code" class="form-label">{{ trans('Kode Pos') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="post_code" id="post_code" value="{{ old('post_code') }}" class="form-control @error('post_code') is-invalid @enderror" placeholder="{{ trans('Masukkan Kode Pos') }}" onkeypress="return onlyNumber(event)" data-old="{{ old('post_code', $student->village->pos_code) }}" readonly>
            @error('post_code')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label" for="address">{{ trans('Alamat Lengkap') }}</label>
            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="4" placeholder="{{ trans('Masukkan Alamat lengkap') }}">{{ old('address', $student->address) }}</textarea>
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
            <span class="text-danger">*</span>
            <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name', $student->parent_name) }}" class="form-control @error('parent_name') is-invalid @enderror" placeholder="{{ trans('Masukkan Nama Ibu Kandung') }}" onkeypress="return onlyLetter(event)">
            @error('parent_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="parent_phone_number" class="form-label">{{ trans('No. Orang Tua') }}</label>
            <span class="text-danger">*</span>
            <input type="text" name="parent_phone_number" id="parent_phone_number" value="{{ old('parent_phone_number', $student->parent_phone_number) }}" class="form-control @error('parent_phone_number') is-invalid @enderror" placeholder="{{ trans('Masukkan Nomor Aktif') }}" onkeypress="return onlyNumber(event)">
            @error('parent_phone_number')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
@push('javascript')
@vite('resources/js/academics/students/input.js')

<script>
  var regencies_url = "{{ route('locations.regencies', ':province_id') }}"
  var districts_url = "{{ route('locations.districts', ':regency_id') }}"
  var villages_url = "{{ route('locations.villages', ':district_id') }}"
  var pos_code_url = "{{ route('locations.postCodes', ':village_id') }}"

</script>
@endpush
