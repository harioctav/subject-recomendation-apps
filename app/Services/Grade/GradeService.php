<?php

namespace App\Services\Grade;

use App\Models\Student;
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
  public function handleUpdateData(Request $request, int $id);
  public function handleExportData(Student $student);
  public function handleDestroyData(int $id);
}
