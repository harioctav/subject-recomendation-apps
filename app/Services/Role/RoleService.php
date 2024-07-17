<?php

namespace App\Services\Role;

use Illuminate\Http\Request;
use LaravelEasyRepository\BaseService;

interface RoleService extends BaseService
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
  public function handleStoreRole(Request $request);
  public function handleUpdateRole(Request $request, int $id);
}
