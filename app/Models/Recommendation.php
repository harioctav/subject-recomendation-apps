<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
