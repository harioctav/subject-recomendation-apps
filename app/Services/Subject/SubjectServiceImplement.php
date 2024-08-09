<?php

namespace App\Services\Subject;

use InvalidArgumentException;
use App\Imports\SubjectImport;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\Subject\SubjectRepository;

class SubjectServiceImplement extends Service implements SubjectService
{
  public function __construct(
    protected SubjectRepository $mainRepository
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

  /**
   * Function to handle add data to the database. 
   *
   */
  public function handleStoreData($request)
  {
    try {
      DB::beginTransaction();

      // Fetch request data
      $payload = $request->validated();

      if (isset($payload['notes']) && is_array($payload['notes'])) :
        $payload['note'] = implode(',', array_filter($payload['notes']));
      else :
        $payload['note'] = null;
      endif;

      // Save data to database
      $this->mainRepository->create($payload);

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

      // Fetch request data
      $payload = $request->validated();

      if (isset($payload['notes']) && is_array($payload['notes'])) :
        $payload['note'] = implode(',', array_filter($payload['notes']));
      else :
        $payload['note'] = null;
      endif;

      // Find data
      $subject = $this->mainRepository->findOrFail($id);

      // Update data in database
      $this->mainRepository->update($subject->id, $payload);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Import data to database
   * 
   */
  public function handleImportData($request)
  {
    try {
      DB::beginTransaction();

      // Fetch request data
      $payload = $request->validated();

      // Save data to database
      Excel::import(new SubjectImport, $payload['file']);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
