<?php

namespace App\Imports\Grades;

use App\Models\Major;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToCollection, WithHeadingRow
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
      $this->gradeImport->addError('Sheet Mahasiswa tidak boleh dikosongkan');
      return;
    }

    $requiredColumns = ['nim', 'mahasiswa', 'program_studi'];
    $headers = $collection->first()->keys()->toArray();

    foreach ($requiredColumns as $column) {
      if (!in_array($column, $headers)) {
        $this->gradeImport->addError("Kolom '{$column}' tidak ditemukan di sheet Mahasiswa");
      }
    }

    $student = $collection->first()->toArray();
    $this->gradeImport->setStudent($student);
  }
}
