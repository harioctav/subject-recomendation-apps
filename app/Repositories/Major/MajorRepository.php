<?php

namespace App\Repositories\Major;

use LaravelEasyRepository\Repository;

interface MajorRepository extends Repository
{
  public function getQuery();
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
}
