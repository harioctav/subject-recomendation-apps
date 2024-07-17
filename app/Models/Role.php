<?php

namespace App\Models;

use App\Traits\Uuid;
use App\Helpers\Enums\RoleType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends SpatieRole
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
   * Definisikan permissions count dalam fungsi.
   *
   * @return string
   */
  public function definePermissionCount(): string
  {
    if ($this->name === RoleType::ADMINISTRATOR->value) {
      return "<span class='badge text-primary'>Memiliki Semua Hak Akses</span>";
    } else {
      return "<span class='badge text-dark'>{$this->permissions->count()} Hak Akses</span>";
    }
  }
}
