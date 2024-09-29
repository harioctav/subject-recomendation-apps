<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GradesImport implements ToCollection, WithHeadingRow
{
  protected $errors = [];
  protected $nim;
  protected $major;
  protected $courses = [];

  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    DB::beginTransaction();
    try {

      $subjects = [];
      $currentSemester = 0;

      $collection->each(function ($row) use (&$subjects, &$currentSemester) {
        if (isset($row['kode_matakuliah']) && preg_match('/^Semester (\d+)$/', $row['kode_matakuliah'], $matches)) {
          $currentSemester = (int) $matches[1];
          return;
        }

        // Tangkap nim
        if (isset($row['kode_matakuliah']) && strpos($row['kode_matakuliah'], 'NIM :') !== false) {
          $this->nim = trim(str_replace('NIM :', '', $row['kode_matakuliah']));
          return;
        }

        // Tangkap major
        if (isset($row['kode_matakuliah']) && strpos($row['kode_matakuliah'], 'PROGRAM STUDI :') !== false) {
          $this->major = trim(str_replace('PROGRAM STUDI :', '', $row['kode_matakuliah']));
          return;
        }

        // Validasi jika nim atau prodi kosong
        if (empty($this->nim) || empty($this->major)) {
          $this->addError("
          Nim atau Program Studi tidak boleh dikosongkan<br>
          Format:<br>
          NIM(spasi):(spasi)NIM MAHASISWA<br>
          PROGRAM STUDI(spasi):(spasi)JURUSAN MAHASISWA");
        }

        // Pastikan row tidak kosong
        $hasValidData = isset($row['kode_matakuliah']) || isset($row['matakuliah']) || isset($row['sks']) ||
          isset($row['nilai']) || isset($row['nilai_mutu']) || isset($row['kelulusan']) || isset($row['masa_ujian']);

        if ($hasValidData) {
          $subjects[$currentSemester][] = [
            'kode_matakuliah' => isset($row['kode_matakuliah']) ? trim($row['kode_matakuliah']) : null,
            'matakuliah' => isset($row['matakuliah']) ? trim($row['matakuliah']) : null,
            'sks' => isset($row['sks']) ? (int) trim($row['sks']) : null,
            'nilai' => isset($row['nilai']) ? trim($row['nilai']) : null,
            'nilai_mutu' => isset($row['nilai_mutu']) ? trim($row['nilai_mutu']) : null,
            'kelulusan' => isset($row['kelulusan']) ? trim($row['kelulusan']) : null,
            'masa_ujian' => isset($row['masa_ujian']) ? trim($row['masa_ujian']) : null,
          ];
        }
      });

      $this->courses = $subjects;

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error importing students: ' . $e->getMessage());
      $this->errors[] = 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage();
    }
  }

  public function addError($message)
  {
    $this->errors = $message;
  }

  public function getErrors()
  {
    // return array_filter($this->errors);
    return $this->errors;
  }

  public function getNim()
  {
    return $this->nim;
  }

  public function getMajor()
  {
    return $this->major;
  }

  public function getCourses()
  {
    return $this->courses;
  }
}
