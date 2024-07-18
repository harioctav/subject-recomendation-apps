<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum SemesterLevelType: string
{

  use EnumsToArray;

  case ONE = 'Semester Satu';
  case TWO = 'Semester Dua';
  case THREE = 'Semester Tiga';
  case FOUR = 'Semester Empat';
  case FIVE = 'Semester Lima';
  case SIX = 'Semester Enam';
  case SEVEN = 'Semester Tujuh';
  case EIGHT = 'Semester Delapan';
}
