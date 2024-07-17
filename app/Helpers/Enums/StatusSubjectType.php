<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum StatusSubjectType: string
{
  use EnumsToArray;

  case I = 'Inti';
  case N = 'Non Inti';
}
