<?php

namespace App\Services\User;

use App\Helpers\Enums\AccountStatusType;
use App\Helpers\Helper;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Role\RoleRepository;
use App\Repositories\User\UserRepository;

class UserServiceImplement extends Service implements UserService
{
  public function __construct(
    protected UserRepository $mainRepository,
    protected RoleRepository $roleRepository,
  ) {
    // 
  }

  /**
   * Get query from model User
   *
   */
  public function getquery()
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
   * Get data user from model User by name where have active status
   *
   */
  public function getUserByRoleName($name)
  {
    try {
      return $this->mainRepository->getUserByRoleName($name);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleStoreData($request)
  {
    try {
      DB::beginTransaction();

      // Fetch request data
      $payload = $request->validated();

      // Find Role
      $role = $this->roleRepository->findOrFail($payload['roles']);

      // Handle Upload File
      $avatar = Helper::uploadFile(
        $request,
        "images/users"
      );

      // Save data to database
      $payload['avatar'] = $avatar;
      $payload['password'] = Helper::NEW_PASSWORD;
      $user = $this->mainRepository->create($payload);

      // Give User Role
      $user->assignRole($role->name);

      // Activity Log
      Helper::log(
        trans('activity.users.create', ['user' => $user->name]),
        me()->id,
        'user_activity_store',
        [
          'data' => $user
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
   * Function to handle update data to the database. 
   *
   */
  public function handleUpdateData($request, $id)
  {
    try {
      DB::beginTransaction();

      // Fetch Data
      $payload = $request->validated();

      // Find user & role in database
      $user = $this->mainRepository->findOrFail($id);
      $role = $this->roleRepository->findOrFail($payload['roles']);

      // Cek jika user memiliki role maka hapus role nya dan gantikan dengan role baru
      if ($payload['roles'] != null) :
        $user->removeRole($user->getRoleId());
        $user->assignRole($role->name);
      endif;

      // Handle untuk perubahan avatar
      $avatar = Helper::uploadFile($request, "images/users", $user->avatar);

      // Ubah data di database
      $payload['avatar'] = $avatar;
      $user->update($payload);

      // Activity Log
      Helper::log(
        trans('activity.users.edit', ['user' => $user->name]),
        me()->id,
        'user_activity_update',
        [
          'data' => $user
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
   * Update status user account.
   *
   */
  public function handleChangeStatus($id)
  {
    try {
      DB::beginTransaction();

      // Find User
      $user = $this->findOrFail($id);
      $oldStatus = $user->status;

      // Determine New Status
      $newStatus = $oldStatus == AccountStatusType::ACTIVE->value
        ? AccountStatusType::INACTIVE->value
        : AccountStatusType::ACTIVE->value;

      // Change Status
      $this->mainRepository->update($id, ['status' => $newStatus]);

      // Convert Status to Human-Readable Format
      $statusMap = [
        AccountStatusType::ACTIVE->value => "Active",
        AccountStatusType::INACTIVE->value => "Inactive"
      ];

      $oldStatusReadable = $statusMap[$oldStatus] ?? 'Unknown';
      $newStatusReadable = $statusMap[$newStatus] ?? 'Unknown';

      // Activity Log
      Helper::log(
        trans('activity.users.status', ['user' => $user->name]),
        me()->id,
        'user_activity_status',
        [
          'old' => $oldStatusReadable,
          'new' => $newStatusReadable,
        ]
      );

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Delete data in database & delete image (if not null) in storage.
   *
   */
  public function handleDeleteData($id)
  {
    try {
      DB::beginTransaction();

      // Handle delete avatar
      $user = $this->mainRepository->findOrFail($id);

      if ($user->avatar) :
        Storage::delete($user->avatar);
      endif;

      // Activity
      Helper::log(
        trans('activity.users.destroy', ['user' => $user->name]),
        me()->id,
        'user_activity_destroy',
        [
          'data' => $user,
        ]
      );

      $this->mainRepository->delete($user->id);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
