<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum RoleType: string
{
  use EnumsToArray;

  case ADMINISTRATOR = 'Super Admin';
  case ADMIN_REGISTER =  'Tim Registrasi Matakuliah';
  case ADMIN_FINANCE = 'Tim Keuangan';
  case ADMIN_FILING = 'Tim Pemberkasan';
  case ADMIN_PPDB = 'Tim Pendaftaran Mahasiswa Baru';
}
