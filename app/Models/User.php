<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Uuid;
use App\Helpers\Enums\RoleType;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, Uuid, HasRoles;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'name',
    'email',
    'phone',
    'password',
    'avatar',
    'status',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  /**
   * Get the route key for the model.
   */
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  /**
   * Get user default user avatar.
   *
   * @return void
   */
  public function getUserAvatar(): string | Storage
  {
    if (!$this->avatar) {
      return asset('assets/images/placeholders/default-avatar.png');
    }

    return Storage::url($this->avatar);
  }

  /**
   * Get the user role name.
   *
   */
  public function getRoleName(): string
  {
    return $this->roles->implode('name');
  }

  /**
   * Get the user role id.
   *
   */
  public function getRoleId(): int
  {
    return (int) $this->roles->implode('id');
  }

  /**
   * Declar status label.
   *
   * @return Attribute
   */
  public function statusLabel(): Attribute
  {
    $statusLabel = [
      0 => "<span class='badge bg-danger'>Inactive</span>",
      1 => "<span class='badge bg-success'>Active</span>",
    ];

    return Attribute::make(
      get: fn() => $statusLabel[$this->status] ?? 'Tidak Diketahui',
    );
  }

  /**
   * Scope a query to only include active users.
   * 
   */
  public function scopeActive($data)
  {
    return $data->where('status', true);
  }

  public function getActive(): Collection
  {
    return $this->active()->get();
  }

  /**
   * Get all user except :value
   *
   * @param  mixed $query
   * @return void
   */
  public function scopeWhereNotAdmin($query)
  {
    return $query->whereDoesntHave('roles', function ($row) {
      $row->where('name', RoleType::ADMINISTRATOR->value);
    });
  }

  /**
   * Define badge type roles.
   *
   * @return string
   */
  public function getRoleBadge(): string
  {
    $roleName = $this->getRoleName();

    switch ($roleName) {
      case RoleType::ADMINISTRATOR->value:
        $badgeClass = 'badge text-danger';
        break;
      case RoleType::ADMIN_REGISTER->value:
        $badgeClass = 'badge text-info';
        break;
      default:
        $badgeClass = 'badge';
        break;
    }

    return "<span class='{$badgeClass}'>{$roleName}</span>";
  }
}
