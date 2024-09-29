<?php

namespace App\Models;

use App\Helpers\Enums\DegreeType;
use App\Helpers\Enums\SubjectNoteType;
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
    $totalCourseCredit = 0;
    $subjects = $this->subjects;
    $subjectsBySemester = $subjects->groupBy('pivot.semester');

    foreach ($subjectsBySemester as $semester => $subjects) {
      // Pisahkan mata kuliah berdasarkan "PILIH SALAH SATU"
      $withPilihSalahSatu = $subjects->filter(function ($subject) {
        return str_contains($subject->note, SubjectNoteType::PS->value);
      });

      $withoutPilihSalahSatu = $subjects->filter(function ($subject) {
        return !str_contains($subject->note, SubjectNoteType::PS->value);
      });

      // Tambahkan total SKS dari mata kuliah tanpa "PILIH SALAH SATU"
      foreach ($withoutPilihSalahSatu as $subject) {
        $totalCourseCredit += $subject->course_credit; // Mengambil SKS dari kolom course_credit di tabel subjects
      }

      // Jika ada mata kuliah "PILIH SALAH SATU", hanya tambahkan salah satu dari grup ini
      if ($withPilihSalahSatu->isNotEmpty()) {
        $totalCourseCredit += $withPilihSalahSatu->max()->course_credit; // Ambil salah satu SKS dari mata kuliah pilihan
        // $totalCourseCredit += $withPilihSalahSatu->first()->course_credit; // Ambil salah satu SKS dari mata kuliah pilihan
      }
    }

    // Update nilai total_course_credit pada tabel majors
    $this->update(['total_course_credit' => $totalCourseCredit]);
  }

  public function getElectiveSubjectsInfo()
  {
    $subjects = $this->subjects;
    $subjectsBySemester = $subjects->groupBy('pivot.semester');

    $electiveSubjectsInfo = [];

    foreach ($subjectsBySemester as $semester => $subjects) {
      $withPilihSalahSatu = $subjects->filter(function ($subject) {
        return str_contains($subject->note, SubjectNoteType::PS->value);
      });

      foreach ($withPilihSalahSatu as $subject) {
        $electiveSubjectsInfo[] = [
          'semester' => $semester,
          'id' => $subject->id,
          'code' => $subject->code,
          'name' => $subject->name,
          'course_credit' => $subject->course_credit
        ];
      }
    }

    // Urutkan berdasarkan semester
    usort($electiveSubjectsInfo, function ($a, $b) {
      return $a['semester'] - $b['semester'];
    });

    return $electiveSubjectsInfo;
  }

  public function getFormattedElectiveSemesters()
  {
    $electiveSubjects = $this->getElectiveSubjectsInfo();
    $semesters = array_unique(array_column($electiveSubjects, 'semester'));
    sort($semesters);

    $count = count($electiveSubjects);

    if ($count == 0) {
      return [
        'total' => '-',
        'semester' => "Tidak ada Matakuliah Pilihan"
      ];
    }

    $semesters = array_map(function ($semester) {
      return "Semester " . $semester;
    }, $semesters);

    if ($count == 1) {
      return [
        'total' => "{$count} Matakuliah",
        'semester' => implode(', ', $semesters)
      ];
    } else {
      $last = array_pop($semesters);
      $formatted = implode(', ', $semesters);

      return [
        'total' => "{$count} Matakuliah",
        'semester' => "{$formatted} dan {$last}"
      ];
    }
  }
}
