<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\User;

class UserRepositoryImplement extends Eloquent implements UserRepository
{
  public function __construct(
    protected User $model
  ) {
    // 
  }

  /**
   * Get query
   */
  public function getQuery()
  {
    return $this->model->query();
  }

  /**
   * Get data by row name use where or where in function
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
   * Get data user from model User by name where have active status
   *
   */
  public function getUserByRoleName($name)
  {
    return $this->getQuery()->select('*')->whereHas('roles', function ($row) use ($name) {
      $row->where('name', $name);
    })->active();
  }
}
