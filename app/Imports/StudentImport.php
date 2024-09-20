<?php

namespace App\Imports;

use App\Models\Major;
use App\Models\Student;
use App\Models\Village;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentImport implements ToCollection, WithHeadingRow
{
  protected $villageCache;
  protected $errors = [];
  protected $imported = 0;
  protected $skipped = 0;

  public function __construct()
  {
    $this->villageCache = collect();
  }

  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    DB::beginTransaction();

    try {
      $majorCache = collect();
      $existingNims = Student::pluck('nim')->flip();

      foreach ($collection as $row) {
        $villageName = $row['village'];
        $majorName = trim($row['major']);
        $name = trim($row['name']);
        $nim = trim($row['nim']);

        // Auto skip jika kolom major atau name kosong
        if (empty($majorName) || empty($name) || empty($nim)) {
          $this->skipped++;
          continue;
        }

        // Check if NIM already exists
        if ($existingNims->has($nim)) {
          $this->errors = "Beberapa NIM sudah diimport atau sudah tersedia di database. Dan tidak akan diimport ulang.";
          $this->skipped++;
          continue;
        }

        // Check major
        if (!$majorCache->has($majorName)) {
          $major = Major::where('name', $majorName)->first();
          if (!$major) {
            $this->errors[] = "Program Studi '$majorName' tidak ditemukan. Mohon periksa kembali.";
            continue;
          }
          $majorCache->put($majorName, $major->id);
        }

        // Check village
        $villageId = $this->getVillageId($villageName);

        // Convert birth_date
        try {
          $birthDate = $this->convertDate($row['birth_date']);
        } catch (\Exception $e) {
          $this->errors[] = "Format tanggal lahir tidak valid untuk NIM $nim: " . $row['birth_date'];
          continue;
        }

        $import = [
          'nim' => $nim,
          'name' => strtoupper($name),
          'email' => $row['email'],
          'birth_date' => $birthDate,
          'birth_place' => strtoupper($row['birth_place']),
          'gender' => $row['gender'] ? strtolower($row['gender']) : 'unknown',
          'phone' => $row['phone'],
          'religion' => $row['religion'] ? strtolower($row['religion']) : 'unknown',
          'initial_registration_period' => $row['initial_registration_period'],
          'origin_department' => strtoupper($row['origin_department']),
          'upbjj' => strtoupper($row['upbjj']),
          'address' => $row['address'],
          'status' => $row['status'] ?? 'unknown',
          'student_status' => $row['student_status'] ?? 1,
          'parent_name' => $row['parent_name'],
          'parent_phone_number' => $row['parent_phone_name'],
          'major_id' => $majorCache->get($majorName),
          'village_id' => $villageId,
        ];

        // Create new student
        Student::create($import);

        $this->imported++;
        $existingNims->put($nim, true);
      }

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error importing students: ' . $e->getMessage());
      $this->errors[] = 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage();
    }
  }


  protected function getVillageId($villageName)
  {
    // Jika village name kosong, return null
    if (empty($villageName)) {
      return null;
    }

    // Cek apakah village name sudah ada di cache
    if (!$this->villageCache->has($villageName)) {
      // Jika tidak ada di cache, cari di database
      $village = Village::where('name', $villageName)->first();

      if ($village) {
        // Jika ditemukan, simpan id ke cache
        $this->villageCache->put($villageName, $village->id);
      } else {
        // Jika tidak ditemukan, simpan null ke cache
        $this->villageCache->put($villageName, null);
        $this->errors[] = "Data Desa/Kelurahan '$villageName' tidak ditemukan. Mohon periksa kembali.";
      }
    }

    // Return id village dari cache (bisa berupa id atau null)
    return $this->villageCache->get($villageName);
  }

  protected function convertDate($date)
  {
    if ($date) {
      $dateTime = Date::excelToDateTimeObject($date);
      return $dateTime->format('Y-m-d');
    }

    return null;
  }

  public function getErrors()
  {
    // return array_filter($this->errors);
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
