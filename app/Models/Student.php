<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
  use HasFactory, Uuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'major_id',
    'village_id',
    'nim',
    'nik',
    'name',
    'email',
    'birth_date',
    'birth_place',
    'gender',
    'phone',
    'religion',
    'initial_registration_period',
    'upbjj',
    'address',
    'note',
    'avatar',
    'parent_name',
    'parent_phone_number',
  ];

  /**
   * Get the route key for the model.
   */
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  /**
   * Get default student avatar.
   *
   * @return void
   */
  public function getAvatar(): string | Storage
  {
    if (!$this->avatar) {
      return asset('assets/images/placeholders/default-avatar.png');
    }

    return Storage::url($this->avatar);
  }

  /**
   * Get the major that owns the major.
   *
   * @return BelongsTo
   */
  public function major(): BelongsTo
  {
    return $this->belongsTo(Major::class, 'major_id');
  }

  /**
   * Get the village that owns the village.
   *
   * @return BelongsTo
   */
  public function village(): BelongsTo
  {
    return $this->belongsTo(Village::class, 'village_id');
  }

  public function subjects(): BelongsToMany
  {
    return $this->belongsToMany(Subject::class, 'student_grades');
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
   * Get brith day formatted attribute
   */
  public function getFormattedBirthDateAttribute(): string
  {
    // Pastikan bahwa birth_day ada sebelum memformatnya
    if ($this->birth_date) {
      return Carbon::parse($this->birth_date)->translatedFormat('d F Y');
    }

    // Kembalikan nilai default jika birth_day tidak ada
    return null;
  }

  /**
   * Get age of student
   */
  public function getAgeAttribute()
  {
    if ($this->birth_date) {
      return Carbon::parse($this->birth_date)->age;
    }

    return null;
  }
}
