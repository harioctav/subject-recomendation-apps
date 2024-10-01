<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MajorSubject extends Model
{
  use HasFactory, Uuid;

  protected $table = 'major_subject';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'major_id',
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

  public function subject()
  {
    return $this->belongsTo(Subject::class);
  }

  public function major()
  {
    return $this->belongsTo(Major::class);
  }

  protected static function booted()
  {
    static::created(function ($majorSubject) {
      $majorSubject->major->updateTotalCourseCredit();
    });

    static::deleted(function ($majorSubject) {
      $majorSubject->major->updateTotalCourseCredit();
    });
  }
}
