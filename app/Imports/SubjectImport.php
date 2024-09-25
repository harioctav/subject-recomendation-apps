<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectImport implements ToCollection, WithHeadingRow
{
  protected $errors = [];
  protected $skipped = 0;
  protected $imported = 0;

  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    DB::beginTransaction();

    try {
      $existingCode = Subject::pluck('code')->flip();

      foreach ($collection as $row) {
        $code = trim($row['kode_matakuliah']);

        if ($existingCode->has($code)) {
          $this->errors = 'Beberapa Matakuliah sudah diimport atau sudah tersedia di database. Dan tidak akan diimport ulang.';
          $this->skipped++;
          continue;
        }

        $datas = [
          'code' => $code,
          'name' => $row['matakuliah'],
          'course_credit' => $row['sks'] ?? 0,
          'exam_time' => $row['waktu_ujian'],
          'status' => $row['status'],
          'note' => $row['catatan'],
        ];

        // Store to database
        Subject::create($datas);

        $this->imported++;
        $existingCode->put($code, true);
      }

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error importing: ' . $e->getMessage());
      $this->errors[] = 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage();
    }
  }

  public function getErrors()
  {
    return $this->errors;
  }

  public function getSkippedCount()
  {
    return $this->skipped;
  }

  public function getImportedCount()
  {
    return $this->imported;
  }
}
