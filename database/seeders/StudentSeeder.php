<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Student::create([
      'major_id' => 28,
      'village_id' => 69556,
      'nim' => '312019115',
      'nik' => '3326164410800003',
      'name' => 'Joe Biden',
      'email' => 'joe@gmail.com',
      'birth_place' => 'SUKABUMI',
      'birth_date' => '2001-07-22',
      'gender' => 'male',
      'phone' => '087720009887',
      'religion' => 'islam',
      'initial_registration_period' => '2022',
      'address' => 'Jl. Perintis Kemerdekaan No.126, Cibadak, Kec. Cibadak, Kabupaten Sukabumi, Jawa Barat 43351',
      'parent_name' => 'Nana Mirdad',
      'parent_phone_number' => '087756652003'
    ]);
  }
}
