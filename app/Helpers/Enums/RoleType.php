<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum RoleType: string
{
  use EnumsToArray;

  case ADMINISTRATOR = 'Super Admin';
  case ADMIN_REGISTER =  'Tim Registrasi Matakuliah';
}
