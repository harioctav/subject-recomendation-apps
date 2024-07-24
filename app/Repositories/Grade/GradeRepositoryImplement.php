<?php

namespace App\Repositories\Grade;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Grade;

class GradeRepositoryImplement extends Eloquent implements GradeRepository
{
  public function __construct(
    protected Grade $model
  ) {
    // 
  }

  /**
   * Get query
   *
   */
  public function getQuery()
  {
    return $this->model->query();
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
