<?php

namespace App\Repositories\Role;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Role;

class RoleRepositoryImplement extends Eloquent implements RoleRepository
{
  public function __construct(
    protected Role $model
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
   * Get data role by row name
   */
  public function getRoleByName($name = [])
  {
    return $this->getQuery()->select('*')
      ->whereIn('name', $name)
      ->oldest('name');
  }

  /**
   * Look for role data that has permissions
   */
  public function getRoleHasPermissions($id)
  {
    $role = $this->findOrFail($id);
    if ($role) :
      return $role->permissions->pluck('name')->toArray();
    endif;

    return [];
  }
}
