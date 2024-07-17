<?php

namespace Database\Seeders;

use App\Imports\SubjectImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubjectSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $path = public_path('assets/excels/subjects.xlsx');
    Excel::import(new SubjectImport, $path);
  }
}
