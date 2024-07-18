<?php

namespace App\Services\Student;

use Illuminate\Http\Request;
use LaravelEasyRepository\BaseService;

interface StudentService extends BaseService
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
  public function handleDeleteData(int $id);
}
