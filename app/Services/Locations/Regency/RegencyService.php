<?php

namespace App\Services\Locations\Regency;

use LaravelEasyRepository\BaseService;

interface RegencyService extends BaseService
{
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
}
