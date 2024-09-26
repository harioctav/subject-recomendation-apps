<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>HASIL REKOMENDASI MATAKULIAH</title>

  <style>
    @media print {
      body {
        width: 210mm;
        height: 297mm;
        margin: 0;
        padding: 10mm;
        box-sizing: border-box;
      }
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 11pt;
      line-height: 1.3;
    }

    h1 {
      text-align: center;
      font-size: 14pt;
      margin-bottom: 15px;
    }

    .info-container {
      display: flex;
      flex-wrap: wrap;
      margin-bottom: 15px;
    }

    .info-row {
      width: 100%;
      display: flex;
      margin-bottom: 3px;
    }

    .info-col {
      flex: 1;
    }

    .info-label {
      font-weight: bold;
      margin-right: 5px;
    }

    .multi-line {
      flex-basis: 100%;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th,
    td {
      border: 1px solid black;
      padding: 3px;
      text-align: center;
      font-size: 10pt;
    }

    th {
      background-color: #f2f2f2;
    }

    .semester-header {
      font-weight: bold;
      text-align: left;
      padding-left: 5px;
    }

    .left-align {
      text-align: left;
      padding-left: 5px;
    }

    .footer {
      text-align: right;
      margin-top: 20px;
    }

  </style>
</head>
<body>
  <h1>Hasil Rekomendasi Matakuliah</h1>
  <div style="border-top: 1px solid #000000; margin: 1rem 0;"></div>

  <div class="info-container">
    <div class="info-row">
      <div class="info-col">
        <span class="info-label">NIM :</span>
        <span>{{ $student->nim }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">Nama :</span>
        <span>{{ strtoupper($student->name) }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">Program Studi :</span>
        <span>{{ $student->major->code }}/{{ strtoupper($student->major->name) }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">Total SKS <strong>WAJIB</strong> ditempuh :</span>
        <span>{{ $total_course_credit }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">Total SKS <strong>SUDAH</strong> ditempuh :</span>
        <span>{{ $total_course_credit_done }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">Sisa SKS <strong>Yang Belum</strong> ditempuh :</span>
        <span>{{ $total_course_credit_remainder }}</span>
      </div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>No.</th>
        <th>Kode</th>
        <th>Matakuliah</th>
        <th>SKS</th>
        <th>Nilai</th>
        <th>Kel.</th>
        <th>Masa Ujian</th>
        <th>Status</th>
        <th>Pr</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($recommended_subjects as $semesterData)
      <tr>
        <td colspan="9" class="semester-header">{{ $semesterData['semester'] }}</td>
      </tr>
      @foreach ($semesterData['subjects'] as $subject)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $subject['code'] }}</td>
        <td>{{ $subject['name'] }}</td>
        <td>{{ $subject['sks'] }}</td>
        <td>{{ $subject['grade'] }}</td>
        <td>{{ $subject['kelulusan'] }}</td>
        <td>{{ $subject['masa_ujian'] }}</td>
        <td>{{ $subject['status'] }}</td>
        <td>{{ $subject['note'] ?: '-' }}</td>
      </tr>
      @endforeach
      @empty
      <tr style="border-bottom: none;" class="semester-header" style="margin-bottom: 10px">
        <th colspan="9">BELUM ADA MATAKULIAH YANG DIREKOMENDASIKAN</th>
      </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    Tanggal Cetak: {{ $datetime }}
  </div>
</body>
</html>
