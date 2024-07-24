<?php

namespace App\Services\Recommendation;

use Illuminate\Support\Str;
use InvalidArgumentException;
use App\Models\Recommendation;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Subject\SubjectRepository;
use App\Repositories\Recommendation\RecommendationRepository;

class RecommendationServiceImplement extends Service implements RecommendationService
{
  public function __construct(
    protected SubjectRepository $subjectRepository,
    protected StudentRepository $studentRepository,
    protected RecommendationRepository $mainRepository,
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
      // Fetch request
      $payload = $request->validated();

      DB::beginTransaction();

      // Fetch student and its major
      $student = $this->studentRepository->findOrFail($payload['student_id']);
      $major_id = $student->major->id;

      // Prepare data for recommendations
      // $recommendations = [];

      // Store to database
      foreach ($payload['subjects'] as $subject_id) :
        // Get the semester from the pivot table
        $semester = DB::table('major_subject')
          ->where('major_id', $major_id)
          ->where('subject_id', $subject_id)
          ->value('semester');

        // Prepare recommendation data
        // $recommendations[] = [
        //   'uuid' => Str::uuid(),
        //   'student_id' => $student->id,
        //   'subject_id' => (int) $subject_id,
        //   'semester' => $semester,
        // ];

        $this->mainRepository->create([
          'student_id' => $student->id,
          'subject_id' => (int) $subject_id,
          'semester' => $semester,
        ]);
      endforeach;

      // Store recommendations
      // Recommendation::insert($recommendations);

      DB::commit();
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
