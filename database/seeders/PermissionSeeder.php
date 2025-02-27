<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // reset cahced roles and permission
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    $permissions = [
      // Halaman User
      'users.index',
      'users.create',
      'users.store',
      'users.show',
      'users.password',
      'users.edit',
      'users.update',
      'users.destroy',

      // Halaman Role
      'roles.index',
      'roles.edit',
      'roles.update',
      'roles.destroy',

      // Halaman Jurusan
      'majors.index',
      'majors.create',
      'majors.store',
      'majors.show',
      'majors.edit',
      'majors.update',
      'majors.destroy',

      // Halaman Matakuliah
      'subjects.index',
      'subjects.create',
      'subjects.store',
      'subjects.edit',
      'subjects.update',
      'subjects.destroy',

      // Halaman Menambahkan Data Matakuliah ke Jurusan
      'majors.subjects.create',
      'majors.subjects.store',
      'majors.subjects.update',
      'majors.subjects.destroy',

      // Halaman Student
      'students.index',
      'students.create',
      'students.store',
      'students.show',
      'students.edit',
      'students.update',
      'students.destroy',
      'students.import',
      'students.data',
      'students.restore',
      'students.delete',

      // Halaman Rekomendasi
      'recommendations.index',
      'recommendations.create',
      'recommendations.store',
      'recommendations.show',
      'recommendations.export',
      'recommendations.destroy',

      // Halaman Nilai
      'grades.index',
      'grades.create',
      'grades.store',
      'grades.show',
      'grades.edit',
      'grades.update',
      'grades.destroy',
      'grades.export',
      'grades.import',

      // Halaman Activity
      'activities.index',
      'activities.show',

      // Master Data Import
      'imports.store',
    ];

    $guardName = 'web';
    $permissionCategoryId = [
      'users' => 1,
      'roles' => 2,
      'majors' => 3,
      'subjects' => 4,
      'students' => 5,
      'recommendations' => 6,
      'grades' => 7,
      'activities' => 8,
      'imports' => 9
    ];

    foreach ($permissions as $permission) :
      Permission::firstOrCreate([
        'name' => $permission,
        'permission_category_id' => $permissionCategoryId[explode('.', $permission)[0]],
        'guard_name' => $guardName,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    endforeach;
  }
}
