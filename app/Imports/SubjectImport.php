<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SubjectImport implements ToCollection
{
  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    $collection->skip(1)->each(function ($row) {
      $code = $row[0] ?? '';
      $name = $row[1] ?? '';
      $courseCredit = $row[2] ?? '';
      $status = $row[3] ?? '';
      $exam_time = $row[4] ?? '';
      $note = $row[5] ?? '';

      if (!Subject::where('code', $code)->exists()) :
        Subject::create([
          'code' => $code,
          'name' => $name,
          'course_credit' => $courseCredit,
          'status' => $status,
          'exam_time' => $exam_time,
          'note' => $note,
        ]);
      endif;
    });
  }
}
