<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum AccountStatusType: int
{
  use EnumsToArray;

  case ACTIVE = 1;
  case INACTIVE = 0;
}
