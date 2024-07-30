<?php

namespace App\Repositories\Student;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Student;

class StudentRepositoryImplement extends Eloquent implements StudentRepository
{
  public function __construct(
    protected Student $model
  ) {
    $this->model = $model;
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

  /**
   * Get data in model Permission Category with relations
   */
  public function with(array $with = [])
  {
    return $this->model->with($with);
  }
}
