<?php

namespace App\Services\Role;

use App\Helpers\Helper;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use App\Repositories\Role\RoleRepository;

class RoleServiceImplement extends Service implements RoleService
{
  public function __construct(
    protected RoleRepository $mainRepository
  ) {
    // 
  }

  /**
   * Return query for model Role
   *
   */
  public function getQuery()
  {
    try {
      return $this->mainRepository->getQuery();
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
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
    try {
      return $this->mainRepository->getWhere(
        wheres: $wheres,
        columns: $columns,
        comparisons: $comparisons,
        orderBy: $orderBy,
        orderByType: $orderByType
      );
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('alert.log.error'));
    }
  }

  /**
   * Return find role data by row name
   *
   */
  public function getRoleByName($name = [])
  {
    try {
      return $this->mainRepository->getRoleByName($name);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Get data role where has permission
   * 
   */
  public function getRoleHasPermissions($id)
  {
    try {
      return $this->mainRepository->getRoleHasPermissions($id);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Store new data to database
   *
   */
  public function handleStoreRole($request)
  {
    try {
      DB::beginTransaction();
      // Create a new Role & Sync permissions
      $create = $this->mainRepository->create($request->validated());
      $create->syncPermissions($request->permission);

      // create activity
      Helper::log(
        trans('activity.roles.create', ['role' => $create->name]),
        me()->id,
        "role_activity_store",
        [
          'data' => $create->only(['name', 'permissions'])
        ]
      );
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Update a Existing role
   *
   */
  public function handleUpdateRole($request, $id)
  {
    try {
      DB::beginTransaction();
      $role = $this->mainRepository->findOrFail($id); // Find a Role
      $role->update($request->validated());
      $role->syncPermissions($request->permission); // sync with Permissions

      // Activity log
      Helper::log(
        trans('activity.roles.edit', ['role' => $role->name]),
        me()->id,
        'role_activity_update',
        ['old' => $role->only(['name', 'permissions'])]
      );

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
