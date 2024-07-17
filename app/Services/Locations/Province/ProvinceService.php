<?php

namespace App\Services\Locations\Province;

use LaravelEasyRepository\BaseService;

interface ProvinceService extends BaseService
{
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
}
