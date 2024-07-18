<?php

namespace App\Services\PermissionCategory;

use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use App\Repositories\PermissionCategory\PermissionCategoryRepository;

class PermissionCategoryServiceImplement extends Service implements PermissionCategoryService
{
  public function __construct(
    protected PermissionCategoryRepository $mainRepository
  ) {
    // 
  }

  /**
   * Get data query in model Permission Category
   *
   */
  public function query()
  {
    try {
      return $this->mainRepository->query();
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Get data in model Permission Category with relations
   *
   */
  public function with(array $with = [])
  {
    try {
      return $this->mainRepository->with($with);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
