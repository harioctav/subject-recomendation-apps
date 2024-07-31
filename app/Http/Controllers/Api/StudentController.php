<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Enums\GradeType;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\Recommendation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Grade\GradeService;
use App\Services\Major\MajorService;
use Illuminate\Support\Facades\Cache;
use App\Services\Student\StudentService;
use App\Services\Recommendation\RecommendationService;

class StudentController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected MajorService $majorService,
    protected StudentService $studentService,
    protected GradeService $gradeService,
    protected RecommendationService $recommendationService
  ) {
    // 
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Student $student)
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

    return response()->json([
      'student' => $studentData,
      'subjects' => $recommendedSubjects
    ]);
  }

  public function show($student_id)
  {
    $student = $this->studentService->findOrFail($student_id);

    // Hitung total SKS yang sudah ditempuh
    $recommendedSubjects = $this->recommendationService->getWhere(
      wheres: [
        'student_id' => $student->id
      ]
    )->pluck('subject_id');

    // Ambil ID mata kuliah yang dinilai dengan nilai bukan 'E'
    $passedSubjects = Grade::where('student_id', $student_id)
      ->whereIn('subject_id', $recommendedSubjects)
      ->where('grade', '!=', GradeType::E->value)
      ->pluck('subject_id');

    // Hitung total SKS dari mata kuliah yang lulus
    $totalCompletedCourseCredit = Subject::whereIn('id', $passedSubjects)->sum('course_credit');

    $totalCourseCredit = $student->major->total_course_credit;

    $details = [
      'nim' => $student->nim,
      'major_name' => $student->major->name,
      'total_course_credit' => $totalCourseCredit,
      'total_course_credit_done' => $totalCompletedCourseCredit,
      'total_course_credit_remainder' => $totalCourseCredit - $totalCompletedCourseCredit,
      'status' => $student->status
    ];

    return response()->json($details);
  }

  public function courses($student_id)
  {
    $student = $this->studentService->findOrFail($student_id);
    $major_id = $student->major_id;

    // Ambil ID mata kuliah yang sudah direkomendasikan untuk mahasiswa ini
    $recommendedSubjects = Recommendation::where('student_id', $student_id)
      ->select('subject_id')
      ->get();

    $recommendedSubjectIds = $recommendedSubjects->pluck('subject_id')->toArray();

    // Ambil ID mata kuliah dengan nilai 'E' yang sudah direkomendasikan
    $subjectIdsWithEGrade = Grade::where('student_id', $student_id)
      ->whereIn('subject_id', $recommendedSubjectIds)
      ->where('grade', 'E')
      ->pluck('subject_id')
      ->toArray();

    // Query untuk mengambil semua mata kuliah berdasarkan major_id
    $subjects = Subject::whereHas('majors', function ($query) use ($major_id) {
      $query->where('majors.id', $major_id);
    })->with(['majors' => function ($query) use ($major_id) {
      $query->where('majors.id', $major_id);
    }])->get();

    // Mengelompokkan mata kuliah berdasarkan semester
    $subjectsBySemester = $subjects->groupBy(function ($subject) {
      return $subject->majors->first()->pivot->semester;
    })->sortKeys();

    $formattedSubjectsBySemester = [];

    foreach ($subjectsBySemester as $semester => $semesterSubjects) {
      $filteredSubjects = $semesterSubjects->filter(function ($subject) use ($recommendedSubjectIds, $subjectIdsWithEGrade) {
        return !in_array($subject->id, $recommendedSubjectIds) || in_array($subject->id, $subjectIdsWithEGrade);
      });

      if ($filteredSubjects->isNotEmpty()) {
        $formattedSubjectsBySemester[] = [
          'semester' => $semester,
          'subjects' => $filteredSubjects->map(function ($subject) {
            return [
              'id' => $subject->id,
              'subject_name' => $subject->name,
              'sks' => $subject->course_credit
            ];
          })->values()
        ];
      }
    }

    if (empty($formattedSubjectsBySemester)) {
      return response()->json([
        'status' => 'not_found',
        'message' => 'Data Not Found: Semua mata kuliah sudah direkomendasikan'
      ]);
    }

    return response()->json($formattedSubjectsBySemester);
  }
}
