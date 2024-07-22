<?php

namespace App\Services\Recommendation;

use LaravelEasyRepository\BaseService;

interface RecommendationService extends BaseService
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
