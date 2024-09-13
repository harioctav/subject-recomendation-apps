<?php

namespace App\Imports\Grades;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectsImport implements ToCollection
{
  public function __construct(
    protected GradeImport $gradeImport
  ) {
    # code...
  }

  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    $grouped = [];
    $currentSemester = 0;

    $collection->skip(1)->each(function ($row) use (&$grouped, &$currentSemester) {
      if (isset($row[0]) && preg_match('/^Semester (\d+)$/', $row[0], $matches)) {
        $currentSemester = (int) $matches[1];
        return;
      }

      if (isset($row[0]) && isset($row[1]) && $row[0] !== '' && $row[1] !== '') {
        $grouped[$currentSemester][] = [
          'code' => trim($row[0]),
          'name' => trim($row[1]),
          'sks' => isset($row[2]) ? $row[2] : null,
          'grade' => isset($row[3]) ? $row[3] : null,
          'mutu' => isset($row[4]) ? $row[4] : null,
          'exam_period' => isset($row[5]) ? (string)$row[5] : null,
        ];
      }
    });

    // foreach ($collection as $row) {
    //   if (preg_match('/^Semester (\d+)$/', $row[0], $matches)) {
    //     $currentSemester = (int) $matches[1];
    //     continue;
    //   }

    //   if (!empty($row[0]) && !empty($row[1])) {
    //     $grouped[$currentSemester][] = [
    //       'code' => trim($row[0]),
    //       'name' => trim($row[1]),
    //       'sks' => $row[2] ?? null,
    //       'grade' => $row[3] ?? null,
    //       'mutu' => $row[4] ?? null,
    //       'exam_period' => $row[5] ? (string)$row[5] : null,
    //     ];
    //   }
    // }

    $this->gradeImport->setSubjects($grouped);
  }
}
