<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum DegreeType: string
{
  use EnumsToArray;

  case STRATA_ONE = 'Strata I';
  case STRATA_TWO = 'Strata II';
  case DIPLOMA_THREE = 'Diploma III';
  case DIPLOMA_FOUR = 'Diploma IV';
}
