<?php

namespace App\Imports;

use Illuminate\Support\Str;
use App\Models\MajorSubject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MajorSubjectImport implements ToCollection, WithCalculatedFormulas, WithChunkReading
{
  public function collection(Collection $rows)
  {
    $chunks = $rows->chunk(1000);  // Proses 1000 baris per batch

    foreach ($chunks as $chunk) {
      $majorSubjects = $chunk->map(function ($row, $index) {
        return [
          'uuid' => Str::uuid(),
          'major_id' => $row[0] ?? null,
          'subject_id' => $row[2] ?? null,
          'semester' => $row[3] ?? null,
        ];
      })->filter(function ($item) {
        return $item['major_id'] !== null && $item['subject_id'] !== null;
      });

      MajorSubject::insert($majorSubjects->toArray());
    }
  }

  public function chunkSize(): int
  {
    return 1000;
  }
}
