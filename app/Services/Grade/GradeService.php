<?php

namespace App\Services\Grade;

use Illuminate\Http\Request;
use LaravelEasyRepository\BaseService;

interface GradeService extends BaseService
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
  public function handleExportData(Request $request);
}
