<?php

namespace App\Repositories\Regency;

use LaravelEasyRepository\Repository;

interface RegencyRepository extends Repository
{
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
}
