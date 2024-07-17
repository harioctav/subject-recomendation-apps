<?php

namespace App\Services\Role;

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
      DB::beginTransaction();
      return $this->mainRepository->getQuery();
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
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
      DB::beginTransaction();
      return $this->mainRepository->getWhere(
        wheres: $wheres,
        columns: $columns,
        comparisons: $comparisons,
        orderBy: $orderBy,
        orderByType: $orderByType
      );
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
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
      DB::beginTransaction();
      return $this->mainRepository->getRoleByName($name);
      DB::commit();
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
      DB::beginTransaction();
      return $this->mainRepository->getRoleHasPermissions($id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
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
      $this->mainRepository->update($role->id, $request->validated()); // Update a Existing role
      $role->syncPermissions($request->permission); // sync with Permissions
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
