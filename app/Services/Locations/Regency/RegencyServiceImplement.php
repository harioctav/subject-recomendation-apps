<?php

namespace App\Services\Locations\Regency;

use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use App\Repositories\Regency\RegencyRepository;

class RegencyServiceImplement extends Service implements RegencyService
{
  public function __construct(
    protected RegencyRepository $mainRepository
  ) {
    // 
  }


  /**
   * Retrieves records from the repository based on the provided where conditions.
   *
   * @param array $wheres The where conditions to filter the records.
   * @param string|array $columns The columns to retrieve from the records.
   * @param string $comparisons The comparison operator to use in the where conditions.
   * @param string|null $orderBy The column to order the results by.
   * @param string|null $orderByType The order direction (ASC or DESC).
   * @return mixed The retrieved records.
   * @throws InvalidArgumentException If an error occurs while retrieving the records.
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
}
