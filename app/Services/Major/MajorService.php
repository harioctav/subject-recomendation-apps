<?php

namespace App\Services\Major;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Http\Request;
use LaravelEasyRepository\BaseService;

interface MajorService extends BaseService
{
  public function getQuery();
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
  public function handleStoreData(Request $request);
  public function handleUpdateData(Request $request, int $id);
  public function handleImportData(Request $request);
  public function handleDestroyData(int $id);

  // Subject to Major
  public function handleStoreSubjectToMajorData(Request $request, Major $major);
  public function handleImportSubjectToMajorData(Request $request);
  public function handleDestroySubjectToMajorData(Major $major, Subject $subject);
}
