{{-- @dd($groupedSubjects) --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lembar Kemajuan Akademik Mahasiswa</title>
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

  </style>
</head>
<body>
  <h1>Lembar Kemajuan Akademik Mahasiswa</h1>
  <div style="border-top: 1px solid #000000; margin: 1rem 0;"></div>

  <div class="info-container">
    <div class="info-row">
      <div class="info-col">
        <span class="info-label">NIM :</span>
        <span>{{ $student->nim }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">UPBJJ :</span>
        <span>{{ $student->upbjj ?: '-' }}</span>
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
        <span class="info-label">Alamat Mahasiswa :</span>
        <span>{{ strtoupper($student->address) }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">REGIS I :</span>
        <span>{{ $student->initial_registration_period ?? '-' }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">KAB/KOTA :</span>
        <span>{{ strtoupper($student->village->district->regency->type) }} {{ strtoupper($student->village->district->regency->name) }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">Kode Pos :</span>
        <span>{{ $student->village->pos_code }}/{{ strtoupper($student->village->district->name) }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">Jurusan Asal :</span>
        <span>{{ $student->origin_department ?? '-' }}</span>
      </div>
      <div class="info-col">
        <span class="info-label">Tempat, Tanggal Lahir :</span>
        <span>{{ $student->birth_place }}, {{ $student->formatted_birth_date }}</span>
      </div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
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
      @foreach ($groupedSubjects as $semester => $subjects)
      <tr>
        <td colspan="9" class="semester-header">SEMESTER {{ strtoupper($semester) }}</td>
      </tr>
      @foreach ($subjects as $index => $subjectInfo)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $subjectInfo['subject']->code }}</td>
        <td class="left-align">{{ $subjectInfo['subject']->name }}</td>
        <td>{{ $subjectInfo['subject']->course_credit }}</td>
        <td>{{ $subjectInfo['has_grade'] ? $subjectInfo['grade']->grade : '-' }}</td>
        <td>{{ $subjectInfo['mutu'] ?? '-' }}</td>
        <td>
          @if($subjectInfo['has_grade'])
          @if($subjectInfo['grade']->grade == GradeType::E->value)
          BL
          @else
          LL
          @endif
          @else
          BL
          @endif
        </td>
        <td>{{ $subjectInfo['exam_period'] ?? '-' }}</td>
        <td>
          @if($subjectInfo['subject']->status == StatusSubject::I->value)
          I
          @else
          N
          @endif
        </td>
      </tr>
      @endforeach
      @endforeach
    </tbody>
  </table>

  <ol>
    <li>Jumlah sks dalam Kurikulum yang ditempuh : {{ $studentDetail['total_compeleted_by_curiculum'] }} SKS</li>
    <li>Jumlah sks Alih Kredit : {{ $studentDetail['total_compeleted_55555'] }} SKS</li>
    <li>Jumlah sks mtk kesetaraan dan mtk lain yang ditempuh :</li>
    <li>Jumlah sks Total yang ditempuh untuk perhitungan IPK : {{ $studentDetail['total_compeleted_course_credit'] }} SKS</li>
    <li>Jumlah total nilai mutu : {{ $studentDetail['mutu'] }}</li>
    <li>Jumlah Total sks keseluruhan : {{ $studentDetail['total_course_credit'] }} SKS</li>
    <li>IPK : {{ $studentDetail['gpa'] }}</li>
  </ol>

</body>
</html>
