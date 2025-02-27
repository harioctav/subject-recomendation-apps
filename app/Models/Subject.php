<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
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
    'course_credit',
    'exam_time',
    'status',
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
   * Get all majors
   *
   * @return BelongsToMany
   */
  public function majors(): BelongsToMany
  {
    return $this->belongsToMany(Major::class)->withPivot('semester');
  }

  /**
   * Relation to Student Model
   *
   * @return HasMany
   */
  public function recommendations(): HasMany
  {
    return $this->hasMany(Recommendation::class);
  }

  /**
   * Relation to Student Model
   *
   * @return HasMany
   */
  public function grades(): HasMany
  {
    return $this->hasMany(Grade::class);
  }
}
