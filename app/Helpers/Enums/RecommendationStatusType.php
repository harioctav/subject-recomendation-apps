<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum RecommendationStatusType: string
{
  use EnumsToArray;

  case SEMESTER_BERJALAN = 'Semester Berjalan';
  case DIREKOMENDASIKAN = 'Direkomendasikan';
  case PERLU_PERBAIKAN = 'Perlu Perbaikan';
  case DALAM_PERBAIKAN = 'Dalam Perbaikan';
  case LULUS = 'Lulus';
  case SUDAH_DIPERBAIKI = 'Sudah Diperbaiki';
  case REQUEST_PERBAIKAN = 'Permintaan Perbaikan';
}
