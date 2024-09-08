<?php

namespace App\Models;

use App\Helpers\Enums\RecommendationNoteType;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recommendation extends Model
{
  use HasFactory, Uuid;

  public static function boot()
  {
    parent::boot();

    static::deleting(function ($recommendation) {
      $subjectHasGrade = Grade::where('subject_id', $recommendation->subject_id)
        ->where('student_id', $recommendation->student_id)
        ->exists();

      if ($subjectHasGrade) {
        throw new \Exception('Matakuliah ini sudah selesai dilakukan penilaian, tidak dapat menghapus data');
      }
    });
  }

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'student_id',
    'subject_id',
    'semester',
    'exam_period',
    'note'
  ];

  /**
   * Get the route key for the model.
   */
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  /**
   * subject
   *
   * @return BelongsTo
   */
  public function subject(): BelongsTo
  {
    return $this->belongsTo(Subject::class);
  }

  /**
   * student
   *
   * @return BelongsTo
   */
  public function student(): BelongsTo
  {
    return $this->belongsTo(Student::class);
  }

  // Accessor untuk kolom semester
  public function getSemesterAttribute($value)
  {
    $semesters = [
      1 => 'Semester 1',
      2 => 'Semester 2',
      3 => 'Semester 3',
      4 => 'Semester 4',
      5 => 'Semester 5',
      6 => 'Semester 6',
      7 => 'Semester 7',
      8 => 'Semester 8',
    ];

    return $semesters[$value] ?? 'Unknown Semester';
  }

  public function noteLabel(): Attribute
  {
    $first = RecommendationNoteType::FIRST->value;
    $second = RecommendationNoteType::SECOND->value;
    $repair = RecommendationNoteType::REPAIR->value;
    $done = RecommendationNoteType::DONE->value;
    $passed = RecommendationNoteType::PASSED->value;

    $noteLabel = [
      $first => "<span class='badge text-primary'>{$first}</span>",
      $second => "<span class='badge text-danger'>{$second}</span>",
      $repair => "<span class='badge text-warning'>{$repair}</span>",
      $done => "<span class='badge text-success'>{$done}</span>",
      $passed => "<span class='badge text-success'>{$passed}</span>",
    ];

    return Attribute::make(
      get: fn() => $noteLabel[$this->note] ?? 'Tidak Diketahui',
    );
  }
}
