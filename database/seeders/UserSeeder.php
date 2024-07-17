<?php

namespace Database\Seeders;

use App\Models\User;
use App\Helpers\Enums\RoleType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    User::create([
      'name' => 'Super Administrator',
      'email' => 'admin@gmail.com',
      'email_verified_at' => now(),
      'password' => bcrypt('password'), // password
      'status' => true,
    ])->assignRole(RoleType::ADMINISTRATOR->value);
  }
}
