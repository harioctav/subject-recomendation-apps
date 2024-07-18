<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermissionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionCategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categories = [
      'users.name',
      'roles.name',
      'majors.name',
      'subjects.name',
      'students.name',
    ];

    foreach ($categories as $name) :
      PermissionCategory::firstOrCreate([
        'name' => $name,
      ]);
    endforeach;
  }
}
