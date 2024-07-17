<?php

namespace App\Repositories\Village;

use LaravelEasyRepository\Repository;

interface VillageRepository extends Repository
{
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
}
