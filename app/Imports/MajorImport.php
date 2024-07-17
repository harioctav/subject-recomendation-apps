<?php

namespace App\Imports;

use App\Models\Major;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class MajorImport implements ToCollection
{
  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    $collection->skip(1)->each(function ($row) {
      $code = $row[0] ?? '';
      $name = $row[1] ?? '';
      $degree = $row[2] ?? '';
      $total_course_credit = $row[3] ?? null;

      if (!Major::where('code', $code)->where('name', $name)->exists()) {
        Major::create([
          'code'  => $code,
          'name'  => $name,
          'degree' => $degree,
          'total_course_credit' => $total_course_credit,
        ]);
      }
    });
  }
}
