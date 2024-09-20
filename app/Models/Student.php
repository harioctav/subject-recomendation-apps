<?php

namespace App\Models;

use App\Helpers\Enums\RecommendationNoteType;
use App\Helpers\Enums\StudentStatusType;
use App\Traits\Uuid;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
  use HasFactory, Uuid, SoftDeletes;

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
    'origin_department',
    'upbjj',
    'address',
    'status',
    'student_status',
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
    return $this->belongsTo(Village::class);
  }

  public function subjects(): BelongsToMany
  {
    return $this->belongsToMany(Subject::class, 'recommendations', 'student_id', 'subject_id');
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
    return '--';
  }

  /**
   * Get age of student
   */
  public function getAgeAttribute()
  {
    if ($this->birth_date) {
      return Carbon::parse($this->birth_date)->age . " Tahun";
    }

    return '--';
  }

  public function statusLabel(): Attribute
  {
    $rpl = StudentStatusType::RPL->value;
    $nonRpl = StudentStatusType::NON_RPL->value;

    $statusLabel = [
      $rpl => "<span class='badge bg-success'>{$rpl}</span>",
      $nonRpl => "<span class='badge bg-primary'>{$nonRpl}</span>",
    ];

    return Attribute::make(
      get: fn() => $statusLabel[$this->status] ?? "<span class='badge bg-primary'>Tidak Diketahui</span>",
    );
  }

  // Accessor default, tidak diubah (jika diperlukan tetap dalam format GANJIL/GENAP)
  public function getInitialRegistrationPeriodAttribute($value)
  {
    return $value; // Tetap mempertahankan format asli
  }

  // Accessor custom untuk format alternatif (XXXX.1/2)
  public function getFormattedRegistrationPeriodAttribute()
  {
    // Mendapatkan nilai asli dari initial_registration_period
    $value = $this->attributes['initial_registration_period'];

    // Pisahkan tahun dan periode (GANJIL/GENAP)
    $parts = explode(' ', $value);
    $year = $parts[0]; // Mengambil tahun, misalnya XXXX
    $period = $parts[1]; // Mengambil periode, misalnya GANJIL atau GENAP

    // Mengganti periode dari GANJIL/GENAP menjadi 1/2
    $periodNumber = ($period == 'GANJIL') ? '1' : '2';

    // Format menjadi XXXX.1/2
    return "{$year}.{$periodNumber}";
  }

  // Accessor untuk mendapatkan province_id
  public function getProvinceAttribute()
  {
    return optional(optional(optional(optional($this->village)->district)->regency)->province);
  }

  // Accessor untuk mendapatkan regency_id
  public function getRegencyAttribute()
  {
    return optional(optional(optional($this->village)->district)->regency);
  }

  // Accessor untuk mendapatkan district_id
  public function getDistrictAttribute()
  {
    return optional(optional($this->village)->district);
  }

  public function getRemainingCredits()
  {
    $totalCredits = $this->major->total_course_credit;

    $passedCredits = $this->recommendations()
      ->whereNotIn('recommendations.note', [
        RecommendationNoteType::SECOND->value,
        RecommendationNoteType::REPAIR->value,
        RecommendationNoteType::FIRST->value
      ])
      ->join('subjects', 'recommendations.subject_id', '=', 'subjects.id')
      ->sum('subjects.course_credit');

    return $totalCredits - $passedCredits;
  }

  public function getEstimatedRemainingSemesters()
  {
    $remainingCredits = $this->getRemainingCredits();
    $maxCreditsPerSemester = 24;

    $takenCredits = $this->recommendations()
      ->select(DB::raw('SUM(subjects.course_credit) as semester_credits'), 'recommendations.semester')
      ->join('subjects', 'recommendations.subject_id', '=', 'subjects.id')
      ->groupBy('recommendations.semester')
      ->get();

    $estimatedSemesters = 0;

    foreach ($takenCredits as $semester) {
      $creditsThisSemester = min($semester->semester_credits, $maxCreditsPerSemester);
      $remainingCredits -= $creditsThisSemester;
      $estimatedSemesters++;
    }

    while ($remainingCredits > 0) {
      $remainingCredits -= $maxCreditsPerSemester;
      $estimatedSemesters++;
    }

    return $estimatedSemesters;
  }
}
