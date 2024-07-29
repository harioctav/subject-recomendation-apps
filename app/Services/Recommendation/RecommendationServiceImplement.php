<?php

namespace App\Services\Recommendation;

use App\Helpers\Enums\GradeType;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Support\Str;
use InvalidArgumentException;
use App\Models\Recommendation;
use App\Repositories\Grade\GradeRepository;
use App\Repositories\Major\MajorRepository;
use Barryvdh\DomPDF\Facade\Pdf;
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
    protected MajorRepository $majorRepository,
    protected GradeRepository $gradeRepository
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

      // Store to database
      foreach ($payload['subjects'] as $subject_id) :
        // Get the semester from the pivot table
        $semester = DB::table('major_subject')
          ->where('major_id', $major_id)
          ->where('subject_id', $subject_id)
          ->value('semester');

        // Prepare recommendation data
        // Store recommendations
        $this->mainRepository->create([
          'student_id' => $student->id,
          'subject_id' => (int) $subject_id,
          'semester' => $semester,
        ]);
      endforeach;

      DB::commit();
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleExportData($request)
  {
    // Get Payload
    $payload = $request->validated();

    try {
      // Find student data
      $student = $this->studentRepository->findOrFail($payload['student_id']);

      // Ambil mata kuliah yang direkomendasikan untuk mahasiswa
      $recommendedSubjects = $student->recommendations
        ->groupBy('semester')
        ->map(function ($recommendations, $semester) use ($student) {
          $subjects = $recommendations->map(function ($recommendation) use ($student) {
            $subject = $recommendation->subject;
            $grade = $student->grades->firstWhere('subject_id', $subject->id);
            $kelulusan = $grade && $grade->grade !== 'E' ? 'L' : 'BL';

            return [
              'id' => $subject->id,
              'code' => $subject->code,
              'name' => $subject->name,
              'grade' => $grade ? $grade->grade : '--',
              'sks' => $subject->course_credit,
              'kelulusan' => $kelulusan,
              'waktu_ujian' => $subject->exam_time,
              'status' => $subject->status,
              'note' => $subject->note
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

      // Hitung total SKS yang sudah ditempuh (hanya untuk mata kuliah dengan nilai selain 'E')
      $totalCompletedCourseCredit = $student->grades->filter(function ($grade) {
        return $grade->grade !== 'E';
      })->sum('subject.course_credit');

      $totalCourseCredit = $student->major->total_course_credit;

      $data = [
        'student' => $student,
        'total_course_credit' => $totalCourseCredit,
        'total_course_credit_done' => $totalCompletedCourseCredit,
        'total_course_credit_remainder' => $totalCourseCredit - $totalCompletedCourseCredit,
        'recommended_subjects' => $recommendedSubjects,
      ];

      $pdf = Pdf::loadView('exports.recommendation', $data);
      return $pdf->stream('recommendation.pdf');
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
