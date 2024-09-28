<?php

namespace App\Imports\Grades;

use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class GradeImport implements WithMultipleSheets, WithEvents
{
  protected $student;
  protected $subjects;
  protected $errors = [];
  protected $sheetNames = ['Mahasiswa', 'Nilai'];
  protected $processedSheets = [];
  protected $totalSheets = 0;

  public function sheets(): array
  {
    return [
      'Mahasiswa' => new StudentImport($this),
      'Nilai' => new SubjectsImport($this),
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

  public function addError($message)
  {
    $this->errors = $message;
  }

  public function getErrors()
  {
    return $this->errors;
  }

  public function passes()
  {
    return empty($this->errors);
  }

  public function registerEvents(): array
  {
    return [
      BeforeImport::class => function (BeforeImport $event) {
        $this->totalSheets = $event->reader->getSheetCount();
        $this->processedSheets = $event->getReader()->getSheetNames();

        if (!$this->validateSheets()) {
          $this->addError($this->getErrors());
        }
      },
    ];
  }

  public function validateSheets(): bool
  {
    if ($this->totalSheets !== 2) {
      $this->addError("Format Excel salah: File harus memiliki tepat 2 sheet.");
      return false;
    }

    $missingSheets = array_diff($this->sheetNames, $this->processedSheets);
    $extraSheets = array_diff($this->processedSheets, $this->sheetNames);

    if (!empty($missingSheets)) {
      foreach ($missingSheets as $sheet) {
        $this->addError("Sheet '$sheet' tidak ditemukan dalam file Excel.");
      }
      return false;
    }

    if (!empty($extraSheets)) {
      foreach ($extraSheets as $sheet) {
        $this->addError("Sheet '$sheet' tidak diharapkan dalam file Excel.");
      }
      return false;
    }

    return true;
  }
}
