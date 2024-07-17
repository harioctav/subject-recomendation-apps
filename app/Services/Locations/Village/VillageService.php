<?php

namespace App\Services\Locations\Village;

use LaravelEasyRepository\BaseService;

interface VillageService extends BaseService
{
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
}
