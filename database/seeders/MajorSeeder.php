<?php

namespace Database\Seeders;

use App\Imports\MajorImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MajorSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $path = public_path('assets/excels/template-majors.xlsx');
    Excel::import(new MajorImport, $path);
  }
}
