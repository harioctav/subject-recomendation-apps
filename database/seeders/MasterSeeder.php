<?php

namespace Database\Seeders;

use App\Imports\Settings\MasterDataImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class MasterSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $file = public_path('assets/excels/template-master-data-prodi-matakuliah.xlsx');
    Excel::import(new MasterDataImport, $file);
  }
}
