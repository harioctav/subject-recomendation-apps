<?php

namespace App\Imports\Majors;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class SubjectToMajorImport implements ToCollection, WithHeadingRow
{
  protected $errors = [];
  protected $imported = 0;
  protected  $skipped = 0;

  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    DB::beginTransaction();

    try {

      $majors = Major::pluck('id', 'code');
      $subjects = Subject::pluck('id', 'code');

      foreach ($collection as $row) {
        $majorCode = $row['kode_jurusan'];
        $subjectCode = strtoupper(trim($row['kode']));
        $semester = $row['semester'];

        // Pengecekan keberadaan Major
        if (!isset($majors[$majorCode])) {
          $this->errors[] = "Major dengan kode {$majorCode} tidak ditemukan.";
          $this->skipped++;
          continue;
        }

        // Pengecekan keberadaan Subject
        if (!isset($subjects[$subjectCode])) {
          $this->errors[] = "Subject dengan kode {$subjectCode} tidak ditemukan.";
          $this->skipped++;
          continue;
        }

        $majorId = $majors[$majorCode];
        $subjectId = $subjects[$subjectCode];

        // Cek apakah relasi sudah ada
        $exists = DB::table('major_subject')
          ->where('major_id', $majorId)
          ->where('subject_id', $subjectId)
          ->exists();

        if ($exists) {
          $this->skipped++;
          continue;
        }

        // Tambahkan ke tabel pivot
        DB::table('major_subject')->insert([
          'uuid' => Str::uuid(),
          'major_id' => $majorId,
          'subject_id' => $subjectId,
          'semester' => $semester,
        ]);

        $findMajor = Major::findOrFail($majorId);
        $findMajor->updateTotalCourseCredit();

        $this->imported++;
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

  public function getImportedCount()
  {
    return $this->imported;
  }

  public function getSkippedCount()
  {
    return $this->skipped;
  }
}
