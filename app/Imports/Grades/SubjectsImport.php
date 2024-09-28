<?php

namespace App\Imports\Grades;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectsImport implements ToCollection, WithHeadingRow
{
  protected $gradeImport;

  public function __construct(GradeImport $gradeImport)
  {
    $this->gradeImport = $gradeImport;
  }

  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    if ($collection->isEmpty()) {
      $this->gradeImport->addError('Sheet Nilai tidak boleh dikosongkan');
      return;
    }

    $requiredColumns = ['kode_matakuliah', 'matakuliah', 'sks', 'nilai', 'nilai_mutu', 'kelulusan', 'masa_ujian'];
    $headers = $collection->first()->keys()->toArray();

    foreach ($requiredColumns as $column) {
      if (!in_array($column, $headers)) {
        $this->gradeImport->addError("Kolom '{$column}' tidak ditemukan di sheet Nilai");
      }
    }

    $grouped = [];
    $currentSemester = 0;

    $collection->skip(1)->each(function ($row) use (&$grouped, &$currentSemester) {
      if (isset($row['kode_matakuliah']) && preg_match('/^Semester (\d+)$/', $row['kode_matakuliah'], $matches)) {
        $currentSemester = (int) $matches[1];
        return;
      }

      // Pastikan row tidak kosong
      $hasValidData = isset($row['kode_matakuliah']) || isset($row['matakuliah']) || isset($row['sks']) ||
        isset($row['nilai']) || isset($row['nilai_mutu']) || isset($row['kelulusan']) || isset($row['masa_ujian']);

      if ($hasValidData) {
        $grouped[$currentSemester][] = [
          'kode_matakuliah' => isset($row['kode_matakuliah']) ? trim($row['kode_matakuliah']) : null,
          'matakuliah' => isset($row['matakuliah']) ? trim($row['matakuliah']) : null,
          'sks' => isset($row['sks']) ? (int) trim($row['sks']) : null,
          'nilai' => isset($row['nilai']) ? trim($row['nilai']) : null,
          'nilai_mutu' => isset($row['nilai_mutu']) ? trim($row['nilai_mutu']) : null,
          'kelulusan' => isset($row['kelulusan']) ? trim($row['kelulusan']) : null,
          'masa_ujian' => isset($row['masa_ujian']) ? trim($row['masa_ujian']) : null,
        ];
      }
    });

    $this->gradeImport->setSubjects($grouped);
  }
}
