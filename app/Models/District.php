<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
  use HasFactory, Uuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'regency_id',
    'name',
    'code',
    'full_code',
  ];

  /**
   * Get the route key for the model.
   */
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  /**
   * Get the regency that owns the regencies.
   *
   * @return BelongsTo
   */
  public function regency(): BelongsTo
  {
    return $this->belongsTo(Regency::class, 'regency_id');
  }

  /**
   * Get the villages for the district.
   *
   * @return HasMany
   */
  public function villages(): HasMany
  {
    return $this->hasMany(Village::class, 'district_id');
  }
}
