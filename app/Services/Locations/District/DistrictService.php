<?php

namespace App\Services\Locations\District;

use LaravelEasyRepository\BaseService;

interface DistrictService extends BaseService
{
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
}
