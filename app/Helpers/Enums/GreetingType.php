<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum GreetingType: string
{
  use EnumsToArray;

  case MORNING = 'Selamat pagi';
  case EVENING = 'Selamat siang';
  case NIGHT = 'Selamat malam';
}
