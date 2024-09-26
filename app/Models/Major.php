<?php

namespace App\Models;

use App\Helpers\Enums\DegreeType;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Major extends Model
{
  use HasFactory, Uuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'code',
    'name',
    'degree',
    'total_course_credit',
  ];

  /**
   * Get the route key for the model.
   */
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  /**
   * Get Formatted Level string
   *
   * @return void
   */
  public function getFormattedDegreeAttribute()
  {
    $replacements = [
      DegreeType::STRATA_ONE->value => 'S1',
      DegreeType::STRATA_TWO->value => 'S2',
      DegreeType::DIPLOMA_THREE->value => 'D3',
      DegreeType::DIPLOMA_FOUR->value => 'D4',
    ];

    return $replacements[$this->degree] ?? $this->degree;
  }

  /**
   * get all subjects
   *
   * @return BelongsToMany
   */
  public function subjects(): BelongsToMany
  {
    return $this->belongsToMany(Subject::class)->withPivot('semester');
  }

  public function updateTotalCourseCredit()
  {
    $subjects = $this->subjects;

    // Grup matakuliah berdasarkan note 'PILIH SALAH SATU' yang ada di model Subject
    $groupedSubjects = $subjects->groupBy(function ($subject) {
      return strpos($subject->note, 'PILIH SALAH SATU') !== false ? 'pilih_satu' : 'lainnya';
    });

    // Hitung semua matakuliah yang bukan 'PILIH SALAH SATU'
    $totalCredit = $groupedSubjects->get('lainnya', collect())->sum('course_credit');

    // Jika ada matakuliah dengan 'PILIH SALAH SATU', ambil yang course_credit terbesar
    if ($groupedSubjects->has('pilih_satu')) {
      // Kelompokkan berdasarkan grup yang sama (misalnya 'BPR | PILIH SALAH SATU | P')
      $subGroups = $groupedSubjects->get('pilih_satu')->groupBy('note');

      // Pilih matakuliah dengan SKS terbesar dari setiap grup yang ada
      foreach ($subGroups as $subGroup) {
        $maxCredit = $subGroup->max('course_credit');
        $totalCredit += $maxCredit;
      }
    }

    // Update nilai total_course_credit pada tabel majors
    $this->update(['total_course_credit' => $totalCredit]);
  }

  public function getElectiveSubjectsBySemester()
  {
    // Ambil semua matakuliah yang terkait dengan major ini
    $subjects = $this->subjects;

    // Filter matakuliah yang memiliki note 'PILIH SALAH SATU'
    $electiveSubjects = $subjects->filter(function ($subject) {
      return strpos($subject->note, 'PILIH SALAH SATU') !== false;
    });

    // Kelompokkan matakuliah pilihan berdasarkan semester
    $groupedBySemester = $electiveSubjects->groupBy('pivot.semester');

    // Bentuk array dengan nama matakuliah dan jumlahnya per semester
    $result = $groupedBySemester->map(function ($subjectsInSemester, $semester) {
      return [
        'semester' => $semester,
        'subjects' => $subjectsInSemester, // Ambil nama matakuliah
        'count' => $subjectsInSemester->count(), // Hitung jumlah matakuliah
      ];
    });

    return $result;
  }
}
