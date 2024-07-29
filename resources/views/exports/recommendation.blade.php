<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>Rekomendasi Matakuliah</title>

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body {
      margin: 0;
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
    }

    .logo {
      width: 150px;
      height: 150px;
      margin: 0 auto;
    }

    .logo img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .title {
      font-weight: bold;
      font-size: 16px;
      margin: 10px 0;
    }

    .address {
      margin-bottom: 10px;
    }

    .student-info {
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table,
    th,
    td {
      border: 1px solid black;
    }

    th,
    td {
      padding: 8px;
    }

    th {
      background-color: #f2f2f2;
    }

    .semester-title {
      font-weight: bold;
      margin-top: 10px;
    }

    .center-text th,
    .center-text td {
      text-align: center;
    }

    .footer {
      text-align: right;
      margin-top: 20px;
    }

  </style>
</head>
<body>
  <div class="header">
    <div class="logo">
      <img src="{{ public_path('assets/images/logos/logo.png') }}" alt="Logo">
    </div>
    <div class="address">
      Jl. Lingkar Selatan No. 1, RT.001/RW.001, Cimahi, Kec. Cicantayan, Kabupaten Sukabumi, Jawa Barat 43155 Indonesia<br>
      Telp. +62812-1015-7276<br>
      Link Universitas 1 | Link Universitas 2 | email: info@salut.ac.id
    </div>
    <hr>
    <div class="title">REKOMENDASI MATAKULIAH</div>
  </div>

  <div class="body">

    <table>
      <tr>
        <td>Nomor Induk Mahasiswa (NIM)</td>
        <td>{{ $student->nim }}</td>
      </tr>
      <tr>
        <td>Nama Mahasiswa</td>
        <td>{{ $student->name }}</td>
      </tr>
      <tr>
        <td>Program Studi</td>
        <td>{{ $student->major->name }}</td>
      </tr>
    </table>

    <table>
      <tr>
        <td>Total SKS <strong>Wajib</strong> Tempuh</td>
        <td>{{ $total_course_credit }}</td>
      </tr>
      <tr>
        <td>Total SKS <strong>Sudah</strong> ditempuh</td>
        <td>{{ $total_course_credit_done }}</td>
      </tr>
      <tr>
        <td>Sisa SKS <strong>Yang Harus</strong> ditempuh</td>
        <td>{{ $total_course_credit_remainder }}</td>
      </tr>
    </table>

    @foreach($recommended_subjects as $semesterData)
    <h3>Semester {{ $semesterData['semester'] }}</h3>
    <table class="center-text">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Matakuliah</th>
          <th>Nilai</th>
          <th>SKS</th>
          <th>Kelulusan</th>
          <th>Waktu Ujian</th>
          <th>Status</th>
          <th>Pr</th>
        </tr>
      </thead>
      <tbody>
        @foreach($semesterData['subjects'] as $subject)
        <tr>
          <td>{{ $subject['code'] }}</td>
          <td>{{ $subject['name'] }}</td>
          <td>{{ $subject['grade'] }}</td>
          <td>{{ $subject['sks'] }}</td>
          <td>{{ $subject['kelulusan'] }}</td>
          <td>{{ $subject['waktu_ujian'] }}</td>
          <td>{{ $subject['status'] }}</td>
          <td>{{ $subject['note'] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endforeach
  </div>

  <div class="footer">
    Sukabumi, 14 September 2023<br>
    a.n. Direktur UNIVERSITAS ABC<br>
    Ketua Program Studi<br><br><br><br>
    SAMBAH WAHYU, ST., M.Kom
  </div>
</body>
</html>
