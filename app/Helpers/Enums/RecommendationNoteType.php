<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum RecommendationNoteType: string
{
  use EnumsToArray;

  case FIRST = 'Direkomendasikan';
  case SECOND = 'Perlu Perbaikan';
  case REPAIR = 'Dalam Perbaikan';
  case DONE = 'Sudah Diperbaiki';
  case PASSED = 'Lulus';
}
