<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum StudentStatusType: string
{

  use EnumsToArray;

  case RPL = 'RPL';
  case NON_RPL = 'Non RPL';
}
