<?php

namespace App\Services\Student;

use App\Helpers\Enums\GradeType;
use App\Helpers\Helper;
use App\Models\Grade;
use App\Models\Subject;
use App\Repositories\Recommendation\RecommendationRepository;
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
    protected VillageRepository $villageRepository,
    protected RecommendationRepository $recommendationRepository
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

  public function getStudentDataWithRecommendations($student)
  {
    $studentData = [
      'nim' => $student->nim,
      'major' => $student->major->name
    ];

    $existingGrades = $student->grades->pluck('subject_id')->toArray();

    $recommendedSubjects = $student->recommendations
      ->groupBy('semester')
      ->map(function ($recommendations, $semester) use ($existingGrades) {
        $subjects = $recommendations
          ->reject(function ($recommendation) use ($existingGrades) {
            return in_array($recommendation->subject_id, $existingGrades);
          })
          ->map(function ($recommendation) {
            return [
              'id' => $recommendation->subject->id,
              'name' => $recommendation->subject->name
            ];
          });

        return [
          'semester' => $semester,
          'subjects' => $subjects->values()
        ];
      })
      ->filter(function ($semesterData) {
        return $semesterData['subjects']->isNotEmpty();
      })
      ->values();

    return [
      'student' => $studentData,
      'subjects' => $recommendedSubjects
    ];
  }

  public function getStudentDetailedInfo($id)
  {
    $student = $this->findOrFail($id);

    $recommendedSubjects = $this->recommendationRepository->getWhere(
      wheres: [
        'student_id' => $student->id
      ]
    )->pluck('subject_id');

    $passedSubjects = Grade::where('student_id', $id)
      ->whereIn('subject_id', $recommendedSubjects)
      ->where('grade', '!=', GradeType::E->value)
      ->pluck('subject_id');

    $totalCompletedCourseCredit = Subject::whereIn('id', $passedSubjects)->sum('course_credit');

    $totalCourseCredit = $student->major->total_course_credit;

    return [
      'nim' => $student->nim,
      'major_name' => $student->major->name,
      'total_course_credit' => $totalCourseCredit,
      'total_course_credit_done' => $totalCompletedCourseCredit,
      'total_course_credit_remainder' => $totalCourseCredit - $totalCompletedCourseCredit,
      'status' => $student->status
    ];
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

      $student = $this->mainRepository->create($create);

      // Activity Log
      Helper::log(
        trans('activity.students.create', ['student' => $student->name]),
        me()->id,
        'student_activity_store',
        [
          'data' => $student
        ]
      );

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

      // update database
      $student->update($update);

      // Activity Log
      Helper::log(
        trans('activity.students.edit', [
          'student' => $student->name
        ]),
        me()->id,
        'student_activity_update',
        [
          'data' => $student
        ]
      );

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

      // Handle delete
      $student = $this->mainRepository->findOrFail($id);

      // Activity Log
      Helper::log(
        trans('activity.students.destroy', [
          'student' => $student->name
        ]),
        me()->id,
        'student_activity_destroy',
        [
          'data' => $student
        ]
      );

      $student->delete();

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleRestoreData($id)
  {
    try {
      $student = $this->mainRepository->getTrashed($id);
      // Activity Log
      Helper::log(
        trans('activity.students.restore', [
          'student' => $student->name
        ]),
        me()->id,
        'student_activity_restore',
        [
          'data' => $student
        ]
      );
      return $this->mainRepository->handleRestoreData($id);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleForceDeleteData($id)
  {
    try {
      DB::beginTransaction();

      // Find Student
      $student = $this->mainRepository->getTrashed($id);

      // Activity Log
      Helper::log(
        trans('activity.students.delete', [
          'student' => $student->name
        ]),
        me()->id,
        'student_activity_delete',
        [
          'data' => $student
        ]
      );

      if ($student->avatar) :
        Storage::delete($student->avatar);
      endif;

      // Force Delete
      $this->mainRepository->handleForceDeleteData($student->id);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
