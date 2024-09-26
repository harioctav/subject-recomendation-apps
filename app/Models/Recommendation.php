<?php

namespace App\Models;

use App\Helpers\Enums\RecommendationNoteType;
use App\Helpers\Enums\RecommendationStatusType;
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

  public function noteLabel(): Attribute
  {
    $current = RecommendationStatusType::SEMESTER_BERJALAN->value;
    $recommended = RecommendationStatusType::DIREKOMENDASIKAN->value;
    $improvement = RecommendationStatusType::PERLU_PERBAIKAN->value;
    $inRepair = RecommendationStatusType::DALAM_PERBAIKAN->value;
    $passed = RecommendationStatusType::LULUS->value;
    $alreadyRepaired = RecommendationStatusType::SUDAH_DIPERBAIKI->value;
    $request = RecommendationStatusType::REQUEST_PERBAIKAN->value;

    $noteLabel = [
      $current => "<span class='badge text-primary'>{$current}</span>",
      $recommended => "<span class='badge text-corporate'>{$recommended}</span>",
      $improvement => "<span class='badge text-danger'>{$improvement}</span>",
      $inRepair => "<span class='badge text-warning'>{$inRepair}</span>",
      $alreadyRepaired => "<span class='badge text-success'>{$alreadyRepaired}</span>",
      $passed => "<span class='badge text-success'>{$passed}</span>",
      $request => "<span class='badge text-elegance'>{$request}</span>",
    ];

    return Attribute::make(
      get: fn() => $noteLabel[$this->note] ?? 'Tidak Diketahui',
    );
  }
}
