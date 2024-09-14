<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Illuminate\Support\Str;

class Activity extends SpatieActivity
{
  use HasFactory, Uuid;

  protected $table = 'activity_log';

  protected static $titleMap = [
    'student' => 'Mahasiswa',
    'major' => 'Program Studi',
    'subject' => 'Matakuliah',
    'user' => 'Pengguna',
    'role' => 'Role & Permission',
  ];

  protected static $actionMap = [
    'store' => 'Tambah',
    'update' => 'Ubah',
    'destroy' => 'Hapus',
    'restore' => 'Pemulihan',
    'delete' => 'Hapus Permanen',
    'create' => 'Tambah',
  ];

  // Metode untuk mendapatkan titleMap
  public static function getTitleMap()
  {
    return self::$titleMap;
  }

  // Metode untuk mendapatkan actionMap
  public static function getActionMap()
  {
    return self::$actionMap;
  }

  public function getLogNameAttribute($value)
  {
    $parts = explode('_', $value);
    $action = end($parts);
    $subject = implode('_', array_slice($parts, 0, -1));

    $actionText = self::$actionMap[$action] ?? Str::title($action);
    $subjectText = $this->getSubjectText($subject);

    return "{$actionText} Data {$subjectText}";
  }

  protected function getSubjectText($subject)
  {
    foreach (self::$titleMap as $key => $value) {
      if (Str::startsWith($subject, $key)) {
        return $value;
      }
    }

    return Str::title(str_replace('_', ' ', $subject));
  }

  public function causer(): MorphTo
  {
    return $this->morphTo();
  }
}
