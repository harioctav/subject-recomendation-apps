<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProvinceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Path ke file json
    $json = File::get(public_path('assets/json/provinces.json'));

    // Decode JSON ke array
    $data = json_decode($json, true);

    $chunks = array_chunk($data, 1000);
    foreach ($chunks as $chunk) {
      foreach ($chunk as &$item) {
        $item['uuid'] = (string) Str::uuid();
      }

      // Save to database
      Province::insert($chunk);
    }
  }
}
