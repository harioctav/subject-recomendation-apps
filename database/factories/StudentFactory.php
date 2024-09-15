<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\Student;
use Illuminate\Support\Carbon;
use App\Helpers\Enums\GenderType;
use App\Helpers\Enums\ReligionType;
use App\Helpers\Enums\StudentStatusType;
use App\Models\Village;
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
    $fake = fake('id_ID');

    $now = Carbon::now();
    $maxDate = $now->subYears(19)->toDateString();
    $minDate = $now->subYears(25)->toDateString();

    // Nama Lengkap
    $first = $fake->unique()->firstName();
    $last = $fake->lastName();
    $name = "{$first} {$last}";

    // Ambil ID Major secara acak dari tabel Major
    $majorIds = Major::pluck('id');
    $majorId = $majorIds->isEmpty() ? null : $majorIds->random();

    $villageIds = Village::pluck('id');
    $villageId = $villageIds->isEmpty() ? null : $villageIds->random();

    // Email
    $email = strtolower($first) . "@gmail.com";

    // Random Years
    $year = $fake->dateTimeBetween('-4 years', 'now')->format('Y');

    // Random Kurikulum
    $curiculum = $fake->randomElement([
      strtoupper("Genap"),
      strtoupper("Ganjil"),
    ]);

    $datas = [
      'major_id' => $majorId,
      'village_id' => $villageId,
      'nim' => $fake->randomNumber(9, true),
      'name' => $name,
      'email' => $email,
      'birth_place' => strtoupper($fake->city()),
      'birth_date' => $fake->dateTimeBetween($minDate, $maxDate)->format('Y-m-d'),
      'gender' => $fake->randomElement(GenderType::toArray()),
      'phone' => $fake->unique()->e164PhoneNumber(),
      'religion' => $fake->randomElement(ReligionType::toArray()),
      'status' => $fake->randomElement(StudentStatusType::toArray()),
      'address' => $fake->address(),
      'initial_registration_period' => "{$year} {$curiculum}",
    ];

    return $datas;
  }
}
