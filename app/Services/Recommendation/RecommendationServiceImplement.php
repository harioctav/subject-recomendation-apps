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
    $recommendedSubjects = $this->getRecommendedSubjectsForStudent($student->id);
    $subjectsWithEGrade = $this->getSubjectsWithEGrade($student->id, $recommendedSubjects->pluck('subject_id'));

    $subjects = $this->getSubjectsForMajor($majorId);
    $subjectsBySemester = $this->groupSubjectsBySemester($subjects, $majorId);

    return $this->formatSubjectsBySemester($subjectsBySemester, $student, $recommendedSubjects, $subjectsWithEGrade, $sksFilter, $gradeFilter);
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
      foreach ($payload['courses'] as $subject_id) :
        // Get the semester from the pivot table
        $semester = DB::table('major_subject')
          ->where('major_id', $major_id)
          ->where('subject_id', $subject_id)
          ->value('semester');

        // Prepare recommendation data
        // Store recommendations
        $this->mainRepository->create([
          'uuid' => Str::uuid(),
          'student_id' => $student->id,
          'subject_id' => (int) $subject_id,
          'semester' => $semester,
          'exam_period' => $payload['exam_period'],
          'note' => $payload['note'],
        ]);
      endforeach;

      DB::commit();
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleExportData($student)
  {
    try {
      // Ambil mata kuliah yang direkomendasikan untuk mahasiswa
      $recommendedSubjects = $student->recommendations
        ->unique('subject_id')
        ->groupBy('semester')
        ->map(function ($recommendations, $semester) use ($student) {
          $subjects = $recommendations->map(function ($recommendation) use ($student) {
            $subject = $recommendation->subject;
            $grade = $student->grades->firstWhere('subject_id', $subject->id);
            $kelulusan = $grade && $grade->grade !== GradeType::E->value ? 'LL' : 'BL';

            return [
              'id' => $subject->id,
              'code' => $subject->code,
              'name' => $subject->name,
              'grade' => $grade ? $grade->grade : '-',
              'sks' => $subject->course_credit,
              'kelulusan' => $kelulusan,
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

      // Hitung total SKS yang sudah ditempuh (hanya untuk mata kuliah dengan nilai selain 'E')
      $totalCompletedCourseCredit = $student->grades->filter(function ($grade) {
        return $grade->grade !== GradeType::E->value;
      })->sum('subject.course_credit');

      $totalCourseCredit = $student->major->total_course_credit;

      $formattedDate = Carbon::now()->locale('id')->isoFormat('D MMMM YYYY');
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
   * @param int $studentId The ID of the student.
   * @return \Illuminate\Database\Eloquent\Collection The recommended subjects for the student.
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
   * @param int $studentId The ID of the student.
   * @param array $subjectIds The IDs of the subjects to check.
   * @return \Illuminate\Support\Collection The subject IDs where the student has an 'E' grade.
   */
  private function getSubjectsWithEGrade($studentId, $subjectIds)
  {
    return Grade::where('student_id', $studentId)
      ->whereIn('subject_id', $subjectIds)
      ->where('grade', GradeType::E->value)
      ->pluck('subject_id');
  }

  /**
   * Get the subjects for the given major.
   *
   * @param int $majorId The ID of the major.
   * @return \Illuminate\Database\Eloquent\Collection The subjects for the given major.
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
   * @param \Illuminate\Database\Eloquent\Collection $subjects The subjects to group.
   * @param int $majorId The ID of the major.
   * @return \Illuminate\Support\Collection The subjects grouped by semester.
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
   * @param \Illuminate\Support\Collection $subjectsBySemester The subjects grouped by semester.
   * @param \App\Models\Student $student The student to format the subjects for.
   * @param \Illuminate\Support\Collection $recommendedSubjects The recommended subjects for the student.
   * @param \Illuminate\Support\Collection $subjectsWithEGrade The subjects where the student has an 'E' grade.
   * @param string|null $sksFilter The SKS (credit) filter to apply.
   * @param string|null $gradeFilter The grade filter to apply.
   * @return array The formatted subjects by semester.
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
   * @param \Illuminate\Database\Eloquent\Collection $semesterSubjects The subjects to filter.
   * @param \App\Models\Student $student The student to filter the subjects for.
   * @param \Illuminate\Support\Collection $recommendedSubjects The recommended subjects for the student.
   * @param \Illuminate\Support\Collection $subjectsWithEGrade The subjects where the student has an 'E' grade.
   * @param string|null $gradeFilter The grade filter to apply.
   * @return \Illuminate\Database\Eloquent\Collection The filtered subjects.
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
   * @param int $semester The semester number.
   * @return string The name of the semester.
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
