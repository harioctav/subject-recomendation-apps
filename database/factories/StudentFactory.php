<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\Student;
use Illuminate\Support\Carbon;
use App\Helpers\Enums\GenderType;
use App\Helpers\Enums\ReligionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
  protected $model = Student::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $now = Carbon::now();
    $maxDate = $now->subYears(19)->toDateString();
    $minDate = $now->subYears(25)->toDateString();

    // Nama Lengkap
    $first = fake()->unique()->firstName();
    $last = fake()->lastName();
    $name = "{$first} {$last}";

    // Ambil ID Major secara acak dari tabel Major
    $majorIds = Major::pluck('id');
    $majorId = $majorIds->isEmpty() ? null : $majorIds->random();

    // Email
    $email = strtolower($first) . "@gmail.com";

    $datas = [
      'major_id' => $majorId,
      'village_id' => 69556,
      'nim' => fake()->randomNumber(9, true),
      'nik' => fake()->unique()->nik(),
      'name' => $name,
      'email' => $email,
      'birth_place' => fake()->city(),
      'birth_date' => fake()->dateTimeBetween($minDate, $maxDate)->format('Y-m-d'),
      'gender' => fake()->randomElement(GenderType::toArray()),
      'phone' => fake()->unique()->e164PhoneNumber(),
      'religion' => fake()->randomElement(ReligionType::toArray()),
      'initial_registration_period' => fake()->numberBetween(2020, 2024),
      'address' => fake()->address(),
      'parent_name' => $name,
      'parent_phone_number' => fake()->unique()->e164PhoneNumber(),
    ];

    return $datas;
  }
}
