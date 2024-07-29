<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum GradeType: string
{
  use EnumsToArray;

  case A = 'A';
  case A_MIN = 'A-';
  case B = 'B';
  case B_MIN = 'B-';
  case C = 'C';
  case C_MIN = 'C-';
  case D = 'D';
  case E = 'E';
}
