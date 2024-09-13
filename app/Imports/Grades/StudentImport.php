<?php

namespace App\Imports\Grades;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToCollection, WithHeadingRow
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
    $studentRow = $collection->first();
    $student = [
      'nim' => $studentRow['nim'] ?? null,
      'student' => $studentRow['student'] ?? null,
      'major' => $studentRow['major'] ?? null,
    ];

    $this->gradeImport->setStudent($student);
  }
}
