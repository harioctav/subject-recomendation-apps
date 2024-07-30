<?php

namespace App\Repositories\Student;

use LaravelEasyRepository\Repository;

interface StudentRepository extends Repository
{
  public function getQuery();
  public function with(array $with = []);
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
}
