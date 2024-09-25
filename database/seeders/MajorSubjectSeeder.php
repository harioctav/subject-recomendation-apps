<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Imports\Majors\SubjectToMajorImport;
use App\Imports\MajorSubjectImport;
use App\Models\MajorSubject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MajorSubjectSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $path = public_path('assets/excels/template-subject-to-majors.xlsx'); // Path to your Excel file
    Excel::import(new SubjectToMajorImport, $path);
  }
}
