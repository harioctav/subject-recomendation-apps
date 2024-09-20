@extends('layouts.app')
@section('title', trans('page.overview.title'))
@section('hero')
<div class="content content-full">
  <h2 class="content-heading">
    {{ trans('page.overview.title') }}
    <nav class="breadcrumb push my-0">
      <div class="fw-semibold" id="date"></div>
      <div class="fw-semibold ms-2" id="clock"></div>
    </nav>
  </h2>
</div>
@endsection
@section('content')
@if (session('status'))
<div class="row items-push">
  <div class="col-6 col-xl-6">
    @include('components.success-alert')
  </div>
</div>
@endif
<div class="row items-push">
  <div class="col-6 col-xl-3">
    <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
      <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
        <div class="d-none d-sm-block">
          <i class="fa fa-school-circle-check fa-2x opacity-25"></i>
        </div>
        <div>
          <div class="fs-3 fw-semibold">{{ $data['majors'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">
            {{ trans('page.majors.title') }}
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-6 col-xl-3">
    <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
      <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
        <div class="d-none d-sm-block">
          <i class="fa fa-book fa-2x opacity-25"></i>
        </div>
        <div>
          <div class="fs-3 fw-semibold">{{ $data['subjects'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">
            {{ trans('page.subjects.title') }}
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-6 col-xl-3">
    <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-fadein">
      <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
        <div class="d-none d-sm-block">
          <i class="fa fa-graduation-cap fa-2x opacity-25"></i>
        </div>
        <div>
          <div class="fs-3 fw-semibold">{{ $data['students'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">
            {{ trans('page.students.title') }}
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-6 col-xl-3">
    <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
      <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
        <div class="d-none d-sm-block">
          <i class="fa fa-users fa-2x opacity-25"></i>
        </div>
        <div>
          <div class="fs-3 fw-semibold">{{ $data['users'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">
            {{ trans('page.users.title') }}
          </div>
        </div>
      </div>
    </a>
  </div>
</div>
@includeIf('components.form-input-nim')
@endsection
@vite('resources/js/home.js')
@push('javascript')
<script>
  @if($message = session('error'))
  Swal.fire({
    icon: 'error'
    , title: 'Data Tidak Ditemukan'
    , text: '{{ $message }}'
    , confirmButtonText: 'Mengerti'
    , confirmButtonColor: '#E74C3C'
  , })
  @endif


  function showOptions() {
    var nim = document.getElementById("nim").value;
    if (nim) {
      document.getElementById("options").style.display = "block";
      document.getElementById("next-btn").style.display = "none";
    } else {
      // alert('Silakan masukkan NIM terlebih dahulu');
      Swal.fire({
        icon: "warning"
        , title: "Kesalahan!!"
        , text: "Silahkan masukkan NIM terlebih dahulu"
        , confirmButtonText: "Mengerti"
      , });
    }
  }

  function submitForm(action) {
    var form = document.getElementById("nim-form");
    if (action === "data") {
      form.action = "{{ route('students.data') }}";
    } else if (action === "semester-remaining") {
      form.action = "{{ route('students.semester') }}";
    }
    form.submit();
  }

</script>
@endpush
