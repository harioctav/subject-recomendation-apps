<?php

namespace App\Imports;

use App\Helpers\Enums\GenderType;
use App\Helpers\Enums\StudentStatusType;
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

        // Abaikan baris jika semua kolom kosong
        if ($row->filter()->isEmpty()) {
          // Semua kolom di baris ini kosong, lanjutkan ke baris berikutnya
          continue;
        }

        $majorName = trim($row['jurusan']);
        $villageName = trim($row['kelurahan']);
        $nim = trim($row['nim']);
        $nik = trim($row['nik']) ?: null;
        $name = trim($row['nama']);
        $email = trim($row['email']) ?: null;
        $birthDate = trim($row['tanggal_lahir']);
        $birthPlace = trim($row['tempat_lahir']);
        $gender = trim($row['jenis_kelamin']) ?: 'unknown';
        $phoneNumber = trim($row['nomor_wa']) ?: null;
        $religion = trim($row['agama']) ?: 'unknown';
        $initialRegistrationPeriod = trim($row['regis']);
        $originDepartment = trim($row['jurusan_asal']);
        $upbjj = trim($row['upbjj']);
        $address = trim($row['alamat_lengkap']);
        $studentStatus = trim($row['status_kemahasiswaan']) ?: 1;
        $regisStatus = trim($row['status_pendaftaran']) ?: 'unknown';
        $parentName = trim($row['nama_wali']);
        $parentPhoneNumber = trim($row['nomor_telepon_wali']) ?: null;

        // Skip jika tidak mengisi jurusan dan nim
        if (empty($majorName) || empty($nim) || empty($name)) :
          $this->errors = "Kolom 'JURUSAN' dan 'NIM' tidak boleh dikosongkan";
          $this->skipped++;
          continue;
        endif;

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
          $checkBirthDate = $this->convertDate($birthDate);
        } catch (\Exception $e) {
          $this->errors[] = "Format tanggal lahir tidak valid untuk NIM $nim: " . $birthDate;
          continue;
        }

        $gender = !empty($gender) ? match ($gender) {
          'Laki - Laki' => GenderType::MALE->value,
          'Perempuan' => GenderType::FEMALE->value,
          default => GenderType::UNKNOWN->value,
        } : $gender;

        $studentStatus = !empty($studentStatus) ? match ($studentStatus) {
          'Aktif' => 1,
          'Tidak Aktif' => 0,
          default => 1,
        } : $studentStatus;

        $regisStatus = !empty($regisStatus) ? match ($regisStatus) {
          'RPL' => StudentStatusType::RPL->value,
          'Non RPL' => StudentStatusType::NON_RPL->value,
          default => StudentStatusType::UNKNOWN->value,
        } : $regisStatus;

        // Create new student
        Student::create([
          'major_id' => $majorCache->get($majorName),
          'village_id' => $villageId,
          'nim' => $nim,
          'nik' => $nik,
          'name' => strtoupper($name),
          'email' => $email,
          'birth_date' => $checkBirthDate,
          'birth_place' => strtoupper($birthPlace),
          'gender' => $gender,
          'phone' => $phoneNumber,
          'religion' => strtolower($religion),
          'initial_registration_period' => strtoupper($initialRegistrationPeriod),
          'origin_department' => strtoupper($originDepartment),
          'upbjj' => strtoupper($upbjj),
          'address' => strtoupper($address),
          'status' => $regisStatus,
          'student_status' => $studentStatus,
          'parent_name' => $parentName,
          'parent_phone_number' => $parentPhoneNumber,
        ]);

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
