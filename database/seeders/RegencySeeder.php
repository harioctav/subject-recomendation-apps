<?php

namespace Database\Seeders;

use App\Models\Regency;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RegencySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Path ke file json
    $json = File::get(public_path('assets/json/regencies.json'));

    // Decode JSON ke array
    $data = json_decode($json, true);

    $chunks = array_chunk($data, 1000);
    foreach ($chunks as $chunk) {
      foreach ($chunk as &$item) {
        $item['uuid'] = (string) Str::uuid();
      }

      // Save to Database
      Regency::insert($chunk);
    }
  }
}
