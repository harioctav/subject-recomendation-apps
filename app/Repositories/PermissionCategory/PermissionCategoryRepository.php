<?php

namespace App\Repositories\PermissionCategory;

use LaravelEasyRepository\Repository;

interface PermissionCategoryRepository extends Repository
{
  public function query();
  public function with(array $with = []);
}
