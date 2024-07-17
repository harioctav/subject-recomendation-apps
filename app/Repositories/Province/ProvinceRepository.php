<?php

namespace App\Repositories\Province;

use LaravelEasyRepository\Repository;

interface ProvinceRepository extends Repository
{
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
}
