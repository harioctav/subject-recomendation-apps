<?php

namespace App\Services\Recommendation;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\Enums\GradeType;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use App\Repositories\Grade\GradeRepository;
use App\Repositories\Major\MajorRepository;
use App\Helpers\Enums\RecommendationNoteType;
use App\Helpers\Enums\RecommendationStatusType;
use App\Helpers\Helper;
use App\Models\Grade;
use App\Models\Subject;
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

  public function getRecommendedSubjects($student, $sksFilter, $gradeFilter)
  {
    $majorId = $student->major->id;

    // Mengambil data matakuliah yang sudah direkomendasikan
    $recommendedSubjects = $this->mainRepository->getWhere(
      wheres: [
        'student_id' => $student->id
      ],
      columns: [
        'subject_id',
        'note'
      ]
    )->get();

    // Dari data matakuliah tersebut kita ambil subject_id nya dan di cari apakah ada data matakuliah yang mendapatkan nilai E
    $subjectsWithEGrade = Grade::where('student_id', $student->id)
      ->whereIn('subject_id', $recommendedSubjects->pluck('subject_id'))
      ->where('grade', GradeType::E->value)
      ->pluck('subject_id');


    // Mengambil data matakuliah yang ada di prodi tsb
    $subjects = Subject::whereHas('majors', function ($query) use ($majorId) {
      $query->where('majors.id', $majorId);
    })->with(['majors' => function ($query) use ($majorId) {
      $query->where('majors.id', $majorId);
    }])->get();

    // Kemudian matakuliahnya di grouping berdasarkan semester
    $subjectsBySemester = $subjects->groupBy(function ($subject) {
      return $subject->majors->first()->pivot->semester;
    })->sortKeys();

    return $this->formatSubjectsBySemester($subjectsBySemester, $student, $recommendedSubjects, $subjectsWithEGrade, $sksFilter, $gradeFilter);
  }

  public function getRecommendations($student)
  {
    $majorId = $student->major_id;
    $studentId = $student->id;

    $subjects = DB::table('subjects')
      ->join('major_subject', 'subjects.id', '=', 'major_subject.subject_id')
      ->leftJoin('grades', function ($join) use ($studentId) {
        $join->on('subjects.id', '=', 'grades.subject_id')
          ->where('grades.student_id', '=', $studentId);
      })
      ->leftJoin('recommendations', function ($join) use ($studentId) {
        $join->on('subjects.id', '=', 'recommendations.subject_id')
          ->where('recommendations.student_id', '=', $studentId);
      })
      ->select(
        'subjects.id',
        'subjects.name',
        'subjects.code',
        'subjects.note',
        DB::raw('CAST(subjects.course_credit AS SIGNED) as course_credit'),
        'subjects.status',
        'major_subject.semester',
        'grades.grade',
        'recommendations.id as recommendation_id'
      )
      ->where('major_subject.major_id', $majorId)
      ->where(function ($query) {
        $query->whereNull('recommendations.id')
          ->orWhere('grades.grade', 'E');
      })
      ->orderBy('major_subject.semester')
      ->orderBy('subjects.name')
      ->get();

    return $subjects;
  }

  public function handleStoreData($request)
  {
    DB::beginTransaction();
    try {
      $payload = $request->validated();
      $student = $this->studentRepository->findOrFail($payload['student_id']);
      $major_id = $student->major->id;
      $isGradeE = $this->getSubjectsWithEGrade($student->id, $payload['courses'])->exists();
      $addedSubjects = [];

      foreach ($payload['courses'] as $subject_id) {
        $semester = $this->getSemesterForSubject($major_id, $subject_id);
        $recommendation = $this->getOrCreateRecommendation($student->id, $subject_id, $semester, $payload);
        $subject = $this->subjectRepository->findOrFail($subject_id);
        $addedSubjects[] = $this->formatAddedSubject($subject, $semester, $recommendation->note);
      }

      $this->logActivity($student, $addedSubjects, $payload['exam_period']);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  private function getSemesterForSubject($major_id, $subject_id)
  {
    return DB::table('major_subject')
      ->where('major_id', $major_id)
      ->where('subject_id', $subject_id)
      ->value('semester');
  }

  private function getOrCreateRecommendation($student_id, $subject_id, $semester, $payload)
  {
    $recommendation = $this->mainRepository->getWhere([
      'student_id' => $student_id,
      'subject_id' => $subject_id,
    ])->first();

    if (!$recommendation) {
      return $this->mainRepository->create([
        'uuid' => Str::uuid(),
        'student_id' => $student_id,
        'subject_id' => (int) $subject_id,
        'semester' => $semester,
        'exam_period' => $payload['exam_period'],
        'note' => $payload['note']
      ]);
    }

    $note = $this->getSubjectsWithEGrade($student_id, [$subject_id])->exists()
      ? RecommendationStatusType::DALAM_PERBAIKAN->value
      : RecommendationStatusType::REQUEST_PERBAIKAN->value;

    $this->mainRepository->update($recommendation->id, ['note' => $note]);
    return $recommendation;
  }

  private function formatAddedSubject($subject, $semester, $note)
  {
    return [
      'id' => $subject->id,
      'name' => $subject->name,
      'semester' => $semester,
      'note' => $note
    ];
  }

  private function logActivity($student, $addedSubjects, $exam_period)
  {
    Helper::log(
      trans('activity.recommendations.create', [
        'student' => $student->name,
        'recommendation' => implode(', ', array_column($addedSubjects, 'name')),
      ]),
      auth()->id(),
      'recommendation_activity_store',
      [
        'student' => [
          'id' => $student->id,
          'name' => $student->name,
        ],
        'subjects' => $addedSubjects,
        'exam_period' => $exam_period,
      ]
    );
  }

  public function handleExportData($student)
  {
    try {
      // Fetch only recommended subjects that haven't been graded
      $recommendedSubjects = $student->recommendations
        ->whereNotIn('subject_id', $student->grades->pluck('subject_id'))
        ->unique('subject_id')
        ->groupBy('semester')
        ->map(function ($recommendations, $semester) {
          $subjects = $recommendations->map(function ($recommendation) {
            $subject = $recommendation->subject;
            return [
              'id' => $subject->id,
              'code' => $subject->code,
              'name' => $subject->name,
              'grade' => '-',
              'sks' => $subject->course_credit,
              'kelulusan' => 'BL',
              'waktu_ujian' => $subject->exam_time,
              'masa_ujian' => $recommendation->exam_period,
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

      // Calculate total course credits
      $totalCourseCredit = $student->major->total_course_credit;
      $totalCompletedCourseCredit = $student->grades->sum('subject.course_credit');

      $formattedDate = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
      $fileTitle = "{$formattedDate}-{$student->name}-HASIL-REKOMENDASI.pdf";

      $data = [
        'student' => $student,
        'total_course_credit' => $totalCourseCredit,
        'total_course_credit_done' => $totalCompletedCourseCredit,
        'total_course_credit_remainder' => $totalCourseCredit - $totalCompletedCourseCredit,
        'recommended_subjects' => $recommendedSubjects,
        'datetime' => $formattedDate
      ];

      $pdf = Pdf::loadView('exports.recommendation', $data);
      return $pdf->stream($fileTitle);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
  public function handleDestroyData($id)
  {
    try {
      // get recomend data
      $recommendation = $this->mainRepository->findOrFail($id);
      $subject = $this->subjectRepository->findOrFail($recommendation->subject_id);

      // Activity Log
      Helper::log(
        trans('activity.recommendations.destroy', [
          'recommendation' => $subject->name
        ]),
        auth()->id(),
        'recommendation_activity_destroy',
        [
          'data' => $recommendation
        ]
      );

      $recommendation->delete();
      return response()->json([
        'message' => trans('session.delete'),
      ]);
    } catch (\Exception $e) {
      return response()->json(['message' => $e->getMessage()], 400);
    }
  }

  /**
   * Get the recommended subjects for a student.
   *
   */
  private function getRecommendedSubjectsForStudent($studentId)
  {
    return $this->getWhere(
      wheres: ['student_id' => $studentId],
      columns: ['subject_id', 'note']
    )->get();
  }

  /**
   * Get the subjects where the student has an 'E' grade.
   *
   */
  private function getSubjectsWithEGrade($studentId, $subjectIds)
  {
    return Grade::where('student_id', $studentId)
      ->whereIn('subject_id', $subjectIds)
      ->where('grade', GradeType::E->value);
  }

  /**
   * Get the subjects for the given major.
   *
   */
  private function getSubjectsForMajor($majorId)
  {
    return Subject::whereHas('majors', function ($query) use ($majorId) {
      $query->where('majors.id', $majorId);
    })->with(['majors' => function ($query) use ($majorId) {
      $query->where('majors.id', $majorId);
    }])->get();
  }

  /**
   * Groups the given subjects by the semester they are taken in.
   *
   */
  private function groupSubjectsBySemester($subjects, $majorId)
  {
    return $subjects->groupBy(function ($subject) {
      return $subject->majors->first()->pivot->semester;
    })->sortKeys();
  }

  /**
   * Formats the subjects by semester for the given student and recommendation criteria.
   *
   */
  private function formatSubjectsBySemester($subjectsBySemester, $student, $recommendedSubjects, $subjectsWithEGrade, $sksFilter, $gradeFilter)
  {
    $formattedSubjects = [];
    $totalSKS = 0;

    foreach ($subjectsBySemester as $semesterNumber => $semesterSubjects) {
      $filteredSubjects = $this->filterSubjects($semesterSubjects, $student, $recommendedSubjects, $subjectsWithEGrade, $gradeFilter);

      if ($filteredSubjects->isNotEmpty()) {
        $semesterName = $this->getSemesterName($semesterNumber);
        $subjectsForSemester = $this->formatSubjectsForSemester($filteredSubjects, $student, $recommendedSubjects, $subjectsWithEGrade, $sksFilter, $semesterName, $totalSKS);
        $formattedSubjects = array_merge($formattedSubjects, $subjectsForSemester);
      }

      if ($sksFilter && $totalSKS >= intval($sksFilter)) {
        break;
      }
    }

    return $formattedSubjects;
  }

  /**
   * Filters the given subjects based on the specified criteria.
   *
   */
  private function filterSubjects($semesterSubjects, $student, $recommendedSubjects, $subjectsWithEGrade, $gradeFilter)
  {
    return $semesterSubjects->filter(function ($subject) use ($recommendedSubjects, $subjectsWithEGrade, $student, $gradeFilter) {
      $grade = Grade::where('student_id', $student->id)
        ->where('subject_id', $subject->id)
        ->first();

      $gradeValue = $grade ? $grade->grade : null;
      $note = $recommendedSubjects->where('subject_id', $subject->id)->first()->note ?? null;

      $isRecommended = $recommendedSubjects->contains('subject_id', $subject->id);
      $hasEGrade = $subjectsWithEGrade->contains($subject->id);
      $isNotRepair = $note !== RecommendationNoteType::REPAIR->value;

      if ($gradeFilter === '') {
        return $isNotRepair;
      } else {
        return $gradeValue === $gradeFilter && $isNotRepair;
      }
    });
  }

  /**
   * Formats the given subjects for the specified semester.
   *
   */
  private function formatSubjectsForSemester($filteredSubjects, $student, $recommendedSubjects, $subjectsWithEGrade, $sksFilter, $semesterName, &$totalSKS)
  {
    return $filteredSubjects->map(function ($subject) use (&$totalSKS, $sksFilter, $semesterName, $student, $recommendedSubjects, $subjectsWithEGrade) {
      $subjectSKS = intval($subject->course_credit);

      $grade = Grade::where('student_id', $student->id)
        ->where('subject_id', $subject->id)
        ->first();
      $gradeValue = $grade ? $grade->grade : '-';

      $note = $recommendedSubjects->where('subject_id', $subject->id)->first()->noteLabel ?? '-';

      if ($sksFilter && $totalSKS + $subjectSKS > intval($sksFilter)) {
        return null;
      }

      $totalSKS += $subjectSKS;

      return [
        'id' => $subject->id,
        'subject_code' => $subject->code,
        'subject_name' => $subject->name,
        'sks' => (int) $subject->course_credit,
        'note_subject' => $subject->note ?: '-',
        'note' => $note,
        'status' => $subject->status,
        'semester' => $semesterName,
        'grade' => $gradeValue,
        'is_recommended' => $recommendedSubjects->contains('subject_id', $subject->id) && !$subjectsWithEGrade->contains($subject->id)
      ];
    })->filter()->values()->toArray();
  }


  /**
   * Gets the name of the specified semester.
   *
   */
  protected function getSemesterName($semester)
  {
    $semesterNames = [
      1 => 'Semester 1',
      2 => 'Semester 2',
      3 => 'Semester 3',
      4 => 'Semester 4',
      5 => 'Semester 5',
      6 => 'Semester 6',
      7 => 'Semester 7',
      8 => 'Semester 8'
    ];

    return $semesterNames[$semester] ?? 'Semester Tidak Diketahui';
  }
}
