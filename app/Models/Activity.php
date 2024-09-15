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
    'recommendation' => 'Rekomendasi Matakuliah',
    'grade' => 'Nilai Matakuliah'
  ];

  protected static $actionMap = [
    'store' => 'Menambah',
    'update' => 'Mengubah',
    'destroy' => 'Menghapus',
    'restore' => 'Pemulihan',
    'delete' => 'Menghapus Permanen',
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
}
