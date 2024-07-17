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
   * Get data user from model User by name where have active status
   *
   */
  public function getUserByRoleName($name)
  {
    try {
      DB::beginTransaction();
      return $this->mainRepository->getUserByRoleName($name);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
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
      $this->mainRepository->update($id, $payload);

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

      // New Status
      $newStatus = ($user->status == AccountStatusType::ACTIVE->value) ? AccountStatusType::INACTIVE->value : AccountStatusType::ACTIVE->value;

      // Change Status
      $this->mainRepository->update($id, [
        'status' => $newStatus,
      ]);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
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

      $this->mainRepository->delete($user->id);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
