<?php

namespace App\Repositories\PermissionCategory;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\PermissionCategory;

class PermissionCategoryRepositoryImplement extends Eloquent implements PermissionCategoryRepository
{
  public function __construct(
    protected PermissionCategory $model
  ) {
    // 
  }

  /**
   * Get data query in model Permission Category
   */
  public function query()
  {
    return $this->model->query();
  }

  /**
   * Get data in model Permission Category with relations
   */
  public function with(array $with = [])
  {
    return $this->model->with($with);
  }
}
