<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>LEMBAR KEMAJUAN AKADEMIK MAHASISWA</title>

  <link rel="stylesheet" href="{{ public_path('assets/exports/export.css') }}">
</head>
<body>

  <div class="header">
    <div class="logo">
      <img src="{{ public_path('assets/images/logos/logo.png') }}" alt="Logo">
    </div>
    <div class="title">UNIVERSITAS TERBUKA</div>
    <div class="address">
      Jl. Lingkar Selatan No. 1, RT.001/RW.001, Cimahi, Kec. Cicantayan, Kabupaten Sukabumi, Jawa Barat 43155 Indonesia<br>
      Telp. +62812-1015-7276<br>
      Link Universitas 1 | Link Universitas 2 | email: info@salut.ac.id
    </div>
    <div style="border-top: 1px solid #000000; margin: 1rem 0;"></div>
    <div class="title">LEMBAR KEMAJUAN AKADEMIK MAHASISWA</div>
  </div>

  <table class="no-border-table">
    <tr>
      <td>NIM</td>
      <td>{{ $student->nim }}</td>
    </tr>
    <tr>
      <td>Nama</td>
      <td>{{ strtoupper($student->name) }}</td>
    </tr>
    <tr>
      <td>Prodi</td>
      <td>{{ $student->major->code }}/{{ strtoupper($student->major->name) }} - {{ $student->major->formatted_degree }}</td>
    </tr>
    <tr>
      <td>Alamat</td>
      <td>{{ $student->address }}</td>
    </tr>
    <tr>
      <td>Kota/Kab</td>
      <td>{{ strtoupper($student->village->district->regency->type) }} {{ strtoupper($student->village->district->regency->name) }}</td>
    </tr>
    <tr>
      <td>Kode Pos</td>
      <td>{{ $student->village->pos_code }} / {{ strtoupper($student->village->district->name) }}</td>
    </tr>
    <tr>
      <td>UPBJJ</td>
      <td>{{ $student->upbjj ?? '--' }}</td>
    </tr>
    <tr>
      <td>REGIS I</td>
      <td>{{ $student->initial_registration_period ?? '--' }}</td>
    </tr>
    <tr>
      <td>Jurusan Asal</td>
      <td>{{ $student->origin_department ?? '--' }}</td>
    </tr>
    <tr>
      <td>Tempat, Tanggal Lahir</td>
      <td>{{ $student->birth_place }}, {{ $student->formatted_birth_date }}</td>
    </tr>
  </table>

  @foreach($groupedSubjects as $semester => $subjects)
  <table class="border-table" style="margin-top: 30px; border-bottom: none;">
    <thead>
      <tr style="border-bottom: none;" class="semester-header" style="margin-bottom: 10px">
        <th colspan="6" style="border-bottom: none;">SEMESTER {{ strtoupper($semester) }}</th>
      </tr>
    </thead>
  </table>
  <table class="border-table">
    <thead>
      <tr>
        <th>Kode</th>
        <th>Matakuliah</th>
        <th>SKS</th>
        <th>Nilai</th>
        <th>Kel</th>
        <th>ST</th>
      </tr>
    </thead>
    <tbody>
      @foreach($subjects as $index => $subjectInfo)
      <tr>
        <td>{{ $subjectInfo['subject']->code }}</td>
        <td>{{ $subjectInfo['subject']->name }}</td>
        <td>{{ $subjectInfo['subject']->course_credit }}</td>
        <td>{{ $subjectInfo['has_grade'] ? $subjectInfo['grade']->grade : '--' }}</td>
        <td>
          @if($subjectInfo['has_grade'])
          @if($subjectInfo['grade']->grade == 'E')
          BL
          @else
          LL
          @endif
          @else
          BL
          @endif
        </td>
        <td>
          @if($subjectInfo['subject']->status == StatusSubject::I->value)
          I
          @else
          N
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endforeach

  <ol>
    <li>Jumlah sks dalam Kurikulum yang ditempuh : </li>
    <li>Jumlah sks Alih Kredit : </li>
    <li>Jumlah sks mtk kesetaraan dan mtk lain yang ditempuh :</li>
    <li>Jumlah sks Total yang ditempuh untuk perhitungan IPK :</li>
    <li>Jumlah nilai mutu :</li>
    <li>Total sks keseluruhan :</li>
    <li>IPK => </li>
  </ol>

  <div style="border-top: 1px solid #000000; margin: 1rem 0;"></div>

  Keterangan:
  <ul>
    <li>KEL = KELULUSAN</li>
    <li>LL = LULUS</li>
    <li>BL = BELUM LULUS</li>
    <li>ST = STATUS</li>
    <li>I = INTI</li>
    <li>N = NON INTI</li>
    <li>PL = PILIHAN</li>
  </ul>
  Silahkan Cek kebenaran Data Pribadi dan kesesuaian nilanya (f_pk_lkam2023)

</body>
</html>
