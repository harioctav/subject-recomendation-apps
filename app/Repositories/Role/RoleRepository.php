<?php

namespace App\Repositories\Role;

use LaravelEasyRepository\Repository;

interface RoleRepository extends Repository
{
  public function getQuery();
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
  public function getRoleByName(array $name = []);
  public function getRoleHasPermissions(int $id);
}
