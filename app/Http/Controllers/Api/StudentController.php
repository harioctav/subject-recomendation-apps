<?php

namespace App\Http\Controllers\Api;

use App\Models\Major;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\Recommendation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Grade\GradeService;
use App\Services\Major\MajorService;
use App\Services\Recommendation\RecommendationService;
use Illuminate\Support\Facades\Cache;
use App\Services\Student\StudentService;

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
    $details = [
      'nim' => $student->nim,
      'major_name' => $student->major->name,
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

    // Query untuk mengambil semua matakuliah berdasarkan major_id
    $subjects = Subject::whereHas('majors', function ($query) use ($major_id) {
      $query->where('majors.id', $major_id);
    })
      ->with(['majors' => function ($query) use ($major_id) {
        $query->where('majors.id', $major_id);
      }])
      ->get();

    // Mengelompokkan matakuliah berdasarkan semester
    $subjectsBySemester = $subjects->groupBy(function ($subject) {
      return $subject->majors->first()->pivot->semester;
    })->sortKeys();

    // Cari semester terendah yang memiliki mata kuliah yang belum direkomendasikan
    $targetSemester = null;
    foreach ($subjectsBySemester as $semester => $semesterSubjects) {
      $unrecommendedSubjects = $semesterSubjects->whereNotIn('id', $recommendedSubjectIds);
      if ($unrecommendedSubjects->isNotEmpty()) {
        $targetSemester = $semester;
        break;
      }
    }

    if ($targetSemester === null) {
      return response()->json([
        'status' => 'not_found',
        'message' => 'Data Not Found: Semua matakuliah sudah direkomendasikan'
      ]);
    }

    $formattedSubjects = [
      'semester' => $targetSemester,
      'subjects' => $subjectsBySemester[$targetSemester]
        ->whereNotIn('id', $recommendedSubjectIds)
        ->map(function ($subject) {
          return [
            'id' => $subject->id,
            'subject_name' => $subject->name,
          ];
        })->values()
    ];

    return response()->json($formattedSubjects);
  }
}
