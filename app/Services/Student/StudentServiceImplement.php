<?php

namespace App\Services\Student;

use App\Helpers\Helper;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Village\VillageRepository;

class StudentServiceImplement extends Service implements StudentService
{
  public function __construct(
    protected StudentRepository $mainRepository,
    protected VillageRepository $villageRepository
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
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleStoreData($request)
  {
    try {
      DB::beginTransaction();

      // Fetch request form
      $payload = $request->validated();

      // handle get data province, regency, district & village
      $village = $this->villageRepository->findOrFail($payload['village']);

      // handle file upload
      $avatar = Helper::uploadFile(
        $request,
        "images/students"
      );

      // Save data
      $payload['avatar'] = $avatar;
      $payload['village_id'] = $village->id;
      $payload['birth_place'] = strtoupper($payload['birth_place']);
      $payload['parent_name'] = strtoupper($payload['parent_name']);

      // Menghilangkan province, regency, district, dan post_code menggunakan array_except()
      $create = Arr::except($payload, ['province', 'regency', 'district', 'village', 'post_code']);

      $this->mainRepository->create($create);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleUpdateData($request, $id)
  {
    try {
      DB::beginTransaction();

      // Fetch request form
      $payload = $request->validated();

      $student = $this->mainRepository->findOrFail($id);
      $avatar = Helper::uploadFile($request, "images/students", $student->avatar);

      $village = $this->villageRepository->findOrFail($payload['village']);

      $payload['avatar'] = $avatar;
      $payload['village_id'] = $village->id;
      $payload['birth_place'] = strtoupper($payload['birth_place']);
      $payload['parent_name'] = strtoupper($payload['parent_name']);

      // Menghilangkan province, regency, district, dan post_code menggunakan array_except()
      $update = Arr::except($payload, ['province', 'regency', 'district', 'village', 'post_code']);

      $this->mainRepository->update($id, $update);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Delete data in database & delete image (if not null) in storage.
   */
  public function handleDeleteData($id)
  {
    try {
      DB::beginTransaction();

      // Handle delete avatar
      $student = $this->mainRepository->findOrFail($id);

      if ($student->avatar) :
        Storage::delete($student->avatar);
      endif;

      $this->mainRepository->delete($student->id);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
