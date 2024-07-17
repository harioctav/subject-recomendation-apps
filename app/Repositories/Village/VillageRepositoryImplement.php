<?php

namespace App\Repositories\Village;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Village;

class VillageRepositoryImplement extends Eloquent implements VillageRepository
{
  public function __construct(
    protected Village $model
  ) {
    // 
  }

  /**
   * Get data by row name use where or where in function
   *
   */
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  ) {
    $data = $this->model->select($columns);

    if (!empty($wheres)) {
      foreach ($wheres as $key => $value) {
        if (is_array($value)) {
          $data = $data->whereIn($key, $value);
        } else {
          $data = $data->where($key, $comparisons, $value);
        }
      }
    }

    if ($orderBy) {
      $data = $data->orderBy($orderBy, $orderByType);
    }

    return $data;
  }
}
