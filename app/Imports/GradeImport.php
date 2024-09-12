<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GradeImport implements ToCollection, WithHeadingRow
{
  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    $currentSemester = null;
    $groupedData = [];

    foreach ($collection as $row) {
      // Cek apakah row adalah penanda semester (contoh: "Semester 1")
      if (preg_match('/^Semester (\d+)$/', $row['code'], $matches)) {
        // Ambil nomor semester dari hasil regex dan ubah menjadi integer
        $currentSemester = (int) $matches[1];
        continue;
      }

      if (!empty($row['code']) && !empty($row['name'])) {
        $groupedData[$currentSemester][] = [
          'code' => trim($row['code']),
          'name' => trim($row['name']),
          'sks' => $row['sks'] ?? null,
          'grade' => $row['grade'] ?? null,
          'mutu' => $row['mutu'] ?? null,
          'exam_period' => $row['exam_period'] ? (string)$row['exam_period'] : null,
        ];
      }
    }

    return $groupedData;
  }
}
