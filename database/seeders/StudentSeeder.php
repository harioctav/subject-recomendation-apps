<?php

namespace Database\Seeders;

use App\Imports\StudentImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class StudentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $path = public_path('assets/excels/template-students.xlsx');
    Excel::import(new StudentImport, $path);
  }
}
