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

    {{-- Student Info --}}
    @includeIf('components.students.info')
    {{-- Student Info --}}

    <div class="row items-push m-3">
      <div class="col-lg-12">
        <div class="row items-push">
          <div class="col-md-6">
            <span class="info-item">
              <span class="info-label">NIM :</span>
              <span class="info-value">{{ $student->nim }}</span>
            </span>
            <span class="info-item">
              <span class="info-label">Alamat :</span>
              <span class="info-value">{{ strtoupper($student->address) ?: '--' }}</span>
            </span>
            <span class="info-item">
              <span class="info-label">Kab/Kota :</span>
              <span class="info-value">{{ strtoupper($student->regency->type) ?: '--' }} {{ strtoupper($student->regency->name) ?: '--' }}</span>
            </span>
            <div class="info-item">
              <div class="info-label"></div>
              <div class="info-value"></div>
            </div>
            <div class="info-item">
              <div class="info-label">Kode Pos :</div>
              <div class="info-value">{{ optional($student->village)->pos_code ?: '--' }}/{{ strtoupper($student->district->name) ?: '--' }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <span class="info-item">
              <span class="info-label">Program Studi :</span>
              <span class="info-value">{{ $student->major->code }}/{{ strtoupper($student->major->name) }}</span>
            </span>
            <div class="info-item">
              <div class="info-label">UPBJJ :</div>
              <div class="info-value">{{ strtoupper($student->upbjj) ?: '--' }}</div>
            </div>
            <div class="info-item">
              <div class="info-label">Regis :</div>
              <div class="info-value">{{ $student->initial_registration_period ?: '--' }}</div>
            </div>
            <div class="info-item">
              <div class="info-label">Jurusan Asal :</div>
              <div class="info-value">{{ strtoupper($student->origin_department) ?: '--' }}</div>
            </div>
            <div class="info-item">
              <div class="info-label">Tempat, Tanggal Lahir :</div>
              <div class="info-value">{{ $student->birth_place ?: '--' }}, {{ $student->formatted_birth_date }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @can('grades.export')
    <div class="mb-3">
      <a href="{{ route('grades.export', $student) }}" target="_blank" class="btn btn-sm btn-success">
        <i class="fa fa-print fa-sm me-1"></i>
        {{ trans('Cetak Transkrip Nilai') }}
      </a>
    </div>
    @endcan

    <div class="row items-push">
      <div class="col-12">
        <div class="table-responsive">
          <table class="table table-vcenter table-bordered">
            <thead>
              <tr class="text-center">
                <th>No.</th>
                <th>Kode Matakuliah</th>
                <th>Nama Matakuliah</th>
                <th>SKS</th>
                <th>Nilai</th>
                <th>Mutu</th>
                <th>Ket</th>
                <th>Masa</th>
                <th>ST</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($subjects as $semester => $subject)
              <tr>
                <td colspan="9" class="semester-header">SEMESTER {{ strtoupper($semester) }}</td>
              </tr>
              @foreach ($subject as $key => $value)
              <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $value['subject']->code }}</td>
                <td class="left-align">{{ $value['subject']->name }}</td>
                <td class="text-center">{{ $value['subject']->course_credit }}</td>
                <td class="text-center">{{ $value['has_grade'] ? $value['grade']->grade : '-' }}</td>
                <td class="text-center">{{ $value['mutu'] ?? '-' }}</td>
                <td class="text-center">{{ $value['has_grade'] ? ($value['grade']->grade == GradeType::E->value ? 'BL' : 'LL') : 'BL' }}</td>
                <td class="text-center">{{ $value['exam_period'] ?? '-' }}</td>
                <td class="text-center">{{ $value['subject']->status == StatusSubject::I->value ? 'I' : 'N' }}</td>
              </tr>
              @endforeach
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mb-4">
          <ol>
            <li>
              <span class="info-label">Jumlah SKS Dalam Kurikulum Yang Ditempuh :</span>
              <span class="info-value">{{ $detail['total_completed_by_curriculum'] }} SKS</span>
            </li>
            <li>
              <span class="info-label">Jumlah SKS Alih Kredit :</span>
              <span class="info-value">{{ $detail['total_completed_55555'] }} SKS</span>
            </li>
            <li>
              <span class="info-label">Jumlah SKS Total Yang Ditempuh Untuk Perhitungan IPK :</span>
              <span class="info-value">{{ $detail['total_completed_course_credit'] }} SKS</span>
            </li>
            <li>
              <span class="info-label">Jumlah Total Nilai Mutu :</span>
              <span class="info-value">{{ $detail['mutu'] }} SKS</span>
            </li>
            <li>
              <span class="info-label">Jumlah Total SKS Keseluruhan :</span>
              <span class="info-value">{{ $detail['total_course_credit'] }} SKS</span>
            </li>
            <li>
              <span class="info-label">IPK :</span>
              <span class="info-value">{{ $detail['gpa'] }} SKS</span>
            </li>
          </ol>
        </div>

        <div class="mb-4">
          <div style="border-top: 1px solid #000000; margin: 1rem 0;"></div>
        </div>

        <div class="mb-4">
          <h3>Keterangan</h3>
          <ul class="list-unstyled">
            <li>
              <span class="info-label">Kel =</span>
              <span class="info-value">Kelulusan</span>
            </li>
            <li>
              <span class="info-label">LL =</span>
              <span class="info-value">Lulus</span>
            </li>
            <li>
              <span class="info-label">BL =</span>
              <span class="info-value">Belum Lulus</span>
            </li>
            <li>
              <span class="info-label">ST =</span>
              <span class="info-value">Status</span>
            </li>
            <li>
              <span class="info-label">I =</span>
              <span class="info-value">Inti</span>
            </li>
            <li>
              <span class="info-label">N =</span>
              <span class="info-value">Non Inti</span>
            </li>
            <li>
              <span class="info-label">Pl =</span>
              <span class="info-value">Pilihan</span>
            </li>
          </ul>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection
@push('css')
<style>
  .info-item {
    display: flex;
    margin-bottom: 0.2rem;
  }

  .info-label {
    font-weight: bold;
    width: 180px;
    flex-shrink: 0;
  }

  .info-value {
    flex: 1;
  }

  .address-container {
    display: flex;
    flex-direction: column;
  }

  .address-line {
    padding-left: 180px;
  }

</style>
@endpush
