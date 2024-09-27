<?php

namespace App\Services\Student;

use App\Helpers\Enums\GradeType;
use App\Helpers\Helper;
use App\Imports\StudentImport;
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
use Maatwebsite\Excel\Facades\Excel;

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

  /**
   * Get all student data based on the provided request.
   */
  public function getStudentAllData($request)
  {
    try {
      $payload = $request->validated();

      // Get student data
      $student = $this->mainRepository->getWhere(
        wheres: [
          'nim' => $payload['nim']
        ]
      )->first();

      if (empty($student)) {
        return back()->with('error', trans('session.students.nim.not-found', ['nim' => $payload['nim']]));
      }

      $student->with([
        'grades',
        'major.subjects',
      ]);

      $subjects = $student->major->subjects->mapToGroups(function ($subject) use ($student) {
        $grade = $student->grades->firstWhere('subject_id', $subject->id);
        $semester = $subject->pivot->semester;

        $subject->course_credit = ($subject->course_credit === '') ? 0 : $subject->course_credit;

        return [$semester => [
          'subject' => $subject,
          'has_grade' => !is_null($grade),
          'grade' => $grade,
          'mutu' => $grade ? $grade->mutuLabel : null,
          'exam_period' => $grade ? $grade->exam_period : null,
        ]];
      });

      $detail = $this->getStudentAcademicInfo($student->id);

      return view('academics.students.data', compact('student', 'subjects', 'detail'));
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function getSemesterRemainingData($request)
  {
    try {

      $payload = $request->validated();

      // Get student data
      $student = $this->mainRepository->getWhere(
        wheres: [
          'nim' => $payload['nim']
        ]
      )->first();

      if (empty($student)) {
        return back()->with('error', trans('session.students.nim.not-found', ['nim' => $payload['nim']]));
      }

      $detail = $this->getStudentAcademicInfo($student->id);

      return view('academics.students.semester', compact('student', 'detail'));
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
              'code' => $recommendation->subject->code,
              'name' => $recommendation->subject->name,
              'exam_period' => $recommendation->exam_period
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

  public function getStudentAcademicInfo($id)
  {
    // Find student data
    $student = $this->mainRepository->findOrFail($id);

    // Get recommended subjects for the student
    $recommendedSubjects = $this->recommendationRepository->getWhere(
      wheres: [
        'student_id' => $student->id
      ]
    )->pluck('subject_id');

    // Calculate total credits for recommended subjects
    $totalRecommendedCredits = Subject::whereIn('id', $recommendedSubjects)->sum('course_credit');

    // Get passed subjects (excluding grade 'E')
    $passedSubjects = Grade::where('student_id', $student->id)
      ->whereIn('subject_id', $recommendedSubjects)
      ->where('grade', '!=', GradeType::E->value);

    // Calculate credits for different exam periods
    $examPeriod55555 = $passedSubjects->clone()->where('exam_period', '55555')->pluck('subject_id');
    $totalCourseCredit55555 = Subject::whereIn('id', $examPeriod55555)->sum('course_credit');

    $examPeriodByCurriculum = $passedSubjects->clone()->where('exam_period', '!=', '55555')->pluck('subject_id');
    $totalCourseCreditByCurriculum = Subject::whereIn('id', $examPeriodByCurriculum)->sum('course_credit');

    // Calculate total completed credits
    $totalCompletedCourseCredit = Subject::whereIn('id', $passedSubjects->pluck('subject_id'))->sum('course_credit');
    $totalCourseCredit = $student->major->total_course_credit;

    $totalCourseRemainder = $totalCourseCredit - $totalCompletedCourseCredit;

    // Calculate GPA and quality points
    $gpa = Helper::calculateGPA($student->id);
    $mutu = $passedSubjects->sum('mutu');

    $hasGradeE = Grade::where('student_id', $student->id)
      ->where('grade', GradeType::E->value)
      ->exists();

    $percentace = ($gpa / 4) * 100;

    return $student = [
      'student' => $student,
      'total_recommended_credits' => $totalRecommendedCredits,
      'total_completed_55555' => $totalCourseCredit55555,
      'total_completed_by_curriculum' => $totalCourseCreditByCurriculum,
      'total_completed_course_credit' => $totalCompletedCourseCredit,
      'total_course_remainder' => $totalCourseRemainder,
      'total_course_credit' => $totalCourseCredit,
      'gpa' => $gpa,
      'mutu' => rtrim(rtrim(number_format($mutu, 2), '0'), '.'),
      'percentace' => $percentace,
      'has_grade_e' => $hasGradeE,
    ];
  }

  public function handleStoreData($request)
  {
    try {
      DB::beginTransaction();

      // Fetch request form
      $payload = $request->validated();

      if (!empty($payload['village'])) {
        $village = $this->villageRepository->findOrFail($payload['village']);
        $payload['village_id'] = $village->id;
      } else {
        $payload['village_id'] = null;
      }

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

      if (!empty($payload['village'])) {
        $village = $this->villageRepository->findOrFail($payload['village']);
        $payload['village_id'] = $village->id;
      } else {
        $payload['village_id'] = optional($student->village)->id;
      }

      $payload['avatar'] = $avatar;
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

  public function handleImportData($request)
  {
    $payload = $request->validated();

    $file = $payload['file'];
    $import = new StudentImport();
    Excel::import($import, $file);

    $errors = $import->getErrors();
    $importedCount = $import->getImportedCount();
    $skippedCount = $import->getSkippedCount();

    Helper::log(
      trans('activity.students.import'),
      me()->id,
      'student_activity_import'
    );

    // Cek jika terdapat error yang valid
    if (!empty($errors)) {
      return redirect()->back()->withErrors($errors)->with([
        'warning' => 'Import selesai dengan beberapa peringatan.',
        'imported' => $importedCount,
        'skipped' => $skippedCount
      ]);
    }

    // Jika tidak ada error, kembalikan pesan sukses
    return redirect()->back()->with([
      'success' => trans('session.create'),
      'imported' => $importedCount,
      'skipped' => $skippedCount
    ]);
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
