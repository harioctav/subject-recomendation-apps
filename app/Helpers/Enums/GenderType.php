<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum GenderType: string
{
  use EnumsToArray;

  case MALE = 'male';
  case FEMALE = 'female';
}
