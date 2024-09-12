<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Grade extends Model
{
  use HasFactory, Uuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'student_id',
    'subject_id',
    'grade',
    'quality',
    'exam_period',
    'mutu',
    'note',
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

  public function recommendations()
  {
    return $this->hasOne(Recommendation::class, 'student_id', 'student_id')
      ->where('subject_id', $this->subject_id);
  }

  // Method untuk mendapatkan semester dari tabel pivot
  public function getSemesterAttribute()
  {
    // Ambil ID major dari student
    $majorId = $this->student->major_id;

    // Ambil semester dari tabel pivot berdasarkan major dan subject
    $pivot = $this->subject->majors->where('id', $majorId)->first();

    return $pivot ? $pivot->pivot->semester : 'Unknown Semester';
  }

  public function mutuLabel(): Attribute
  {
    return Attribute::make(
      get: fn() => rtrim(rtrim(number_format($this->mutu, 2), '0'), '.')
    );
  }
}
