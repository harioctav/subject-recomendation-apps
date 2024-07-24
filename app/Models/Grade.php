<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    'exam_period',
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

  // Method untuk mendapatkan semester dari tabel pivot
  public function getSemesterAttribute()
  {
    // Ambil ID major dari student
    $majorId = $this->student->major_id;

    // Ambil semester dari tabel pivot berdasarkan major dan subject
    $pivot = $this->subject->majors->where('id', $majorId)->first();

    return $pivot ? $pivot->pivot->semester : 'Unknown Semester';
  }
}
