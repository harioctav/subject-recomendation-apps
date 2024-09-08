<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Helpers\Enums\RoleType;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // reset cahced roles and permission
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    // Role Name
    $datas = RoleType::toArray();

    // Save to roles table
    foreach ($datas as $data) :
      $roles = Role::firstOrCreate([
        'name' => $data,
        'guard_name' => 'web'
      ]);
    endforeach;

    // Give a roles permissions
    $regis = $roles->firstWhere('name', RoleType::ADMIN_REGISTER->value);
    $regis->syncPermissions(
      Permission::whereIn('name', [
        'users.show',
        'users.password',
        'users.update',

        // Halaman Jurusan
        'majors.index',
        'majors.create',
        'majors.store',
        'majors.show',
        'majors.edit',
        'majors.update',
        'majors.import',
        'majors.destroy',

        // Halaman Matakuliah
        'subjects.index',
        'subjects.create',
        'subjects.store',
        'subjects.edit',
        'subjects.update',
        'subjects.import',
        'subjects.destroy',

        // Halaman Menambahkan Data Matakuliah ke Jurusan
        'majors.subjects.create',
        'majors.subjects.store',
        'majors.subjects.destroy',

        // Halaman Student
        'students.index',
        'students.create',
        'students.store',
        'students.show',
        'students.edit',
        'students.update',
        'students.destroy',

        // Halaman Rekomendasi
        'recommendations.index',
        'recommendations.create',
        'recommendations.store',
        'recommendations.show',
        'recommendations.export',

        // Halaman Nilai
        'grades.index',
        'grades.create',
        'grades.store',
        'grades.show',
        'grades.edit',
        'grades.update',
        'grades.destroy',
        'grades.export',
      ])->get()
    );
  }
}
