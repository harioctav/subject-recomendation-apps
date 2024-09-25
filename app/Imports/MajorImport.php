<?php

namespace App\Imports;

use App\Models\Major;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MajorImport implements ToCollection, WithHeadingRow
{
  protected $fails = [];
  protected $skipped = 0;
  protected $imported = 0;

  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    DB::beginTransaction();
    try {

      $this->checkFormat($collection);

      $existingCode = Major::pluck('code')->flip();
      $existingName = Major::pluck('name')->flip();

      foreach ($collection as $row) {
        $code = $row['kode_jurusan'];
        $name = $row['jurusan'];
        $level = $row['jenjang'];

        if ($existingCode->has($code) || $existingName->has($name)) {
          $this->fails = 'Beberapa Matakuliah sudah diimport atau sudah tersedia di database. Dan tidak akan diimport ulang.';
          $this->skipped++;
          continue;
        }

        Major::create([
          'code' => $code,
          'name' => $name,
          'degree' => $level,
        ]);

        $this->imported++;
        $existingCode->put($code, true);
        $existingName->put($name, true);
      }

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error importing: ' . $e->getMessage());
      $this->fails[] = 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage();
    }
  }

  public function checkFormat($collection)
  {
    if (!isset($collection[0]['kode_jurusan']) || !isset($collection[0]['jurusan']) || !isset($collection[0]['jenjang'])) {
      throw new \Exception('Format file Excel tidak sesuai. Pastikan terdapat kolom Kode Jurusan, Jurusan, dan Jenjang.');
    }

    return null;
  }

  public function getFails()
  {
    return $this->fails;
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
