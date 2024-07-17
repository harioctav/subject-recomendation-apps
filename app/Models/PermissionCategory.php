<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionCategory extends Model
{
  use HasFactory, Uuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'name',
  ];

  /**
   * Relationship to Permission Model
   * 
   * @return HasMany
   */
  public function permissions(): HasMany
  {
    return $this->hasMany(Permission::class, 'permission_category_id');
  }
}
