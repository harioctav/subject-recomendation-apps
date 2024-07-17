<?php

namespace App\Models;

use App\Models\PermissionCategory;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
  use HasFactory, Uuid;

  /**
   * Get the route key for the model.
   */
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  /**
   * Relationship to permission category model
   *
   * @return BelongsTo
   */
  public function permission_category(): BelongsTo
  {
    return $this->belongsTo(PermissionCategory::class, 'permission_category_id');
  }
}
