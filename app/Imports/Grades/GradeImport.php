<?php

namespace App\Imports\Grades;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GradeImport implements WithMultipleSheets
{
  protected $student;
  protected $subjects;

  public function sheets(): array
  {
    return [
      'Sheet1' => new SubjectsImport($this),
      'Sheet2' => new StudentImport($this)
    ];
  }

  public function setSubjects($subjects)
  {
    $this->subjects = $subjects;
  }

  public function setStudent($student)
  {
    $this->student = $student;
  }

  public function getSubjects()
  {
    return $this->subjects;
  }

  public function getStudent()
  {
    return $this->student;
  }
}
