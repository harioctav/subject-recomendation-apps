<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Helpers\Enums\RoleType;
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
  }
}
