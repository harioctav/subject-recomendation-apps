<?php

namespace App\Services\Recommendation;

use App\Models\Student;
use Illuminate\Http\Request;
use LaravelEasyRepository\BaseService;

interface RecommendationService extends BaseService
{
  public function getQuery();
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  );
  public function getRecommendedSubjects(Student $student, $sksFilter, $gradeFilter);
  public function getRecommendations(Student $student);
  public function handleStoreData(Request $request);
  public function handleExportData(Student $student);
  public function handleDestroyData(int $id);
}
