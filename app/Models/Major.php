<?php

namespace App\Models;

use App\Helpers\Enums\DegreeType;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
  public function getFormattedLevelAttribute()
  {
    $replacements = [
      DegreeType::STRATA_ONE->value => 'S1',
      DegreeType::STRATA_TWO->value => 'S2',
      DegreeType::DIPLOMA_THREE->value => 'D3',
      DegreeType::DIPLOMA_FOUR->value => 'D4',
    ];

    return $replacements[$this->level] ?? $this->level;
  }
}
