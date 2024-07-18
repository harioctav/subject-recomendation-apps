<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum ReligionType: string
{
  use EnumsToArray;

  case ISLAM = 'islam';
  case CHATOLIC = 'katolik';
  case CHRISTIAN = 'kristen';
  case HINDU = 'hindu';
  case BUDDHA = 'buddha';
}
