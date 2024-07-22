<?php

namespace App\Imports;

use Illuminate\Support\Str;
use App\Models\MajorSubject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MajorSubjectImport implements ToCollection, WithChunkReading
{
  public function collection(Collection $rows)
  {
    $rows->skip(1)->each(function ($row) {
      $major_id = $row[0] ?? null;
      $subject_id = $row[2] ?? null;
      $semester = $row[3] ?? null;

      MajorSubject::create([
        'major_id' => $major_id,
        'subject_id' => $subject_id,
        'semester' => $semester,
      ]);
    });
  }

  public function chunkSize(): int
  {
    return 1000;
  }
}
