<?php

namespace App\Http\Controllers\Evaluations;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\Recommendation;
use App\Helpers\Enums\GradeType;
use App\Http\Controllers\Controller;
use App\Services\Student\StudentService;
use App\DataTables\Evaluations\StudentDataTable;
use App\Services\Recommendation\RecommendationService;
use App\DataTables\Evaluations\RecommendationDataTable;
use App\Http\Requests\Evaluations\RecommendationRequest;
use App\Http\Requests\Evaluations\RecommendationExportRequest;

class RecommendationController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected StudentService $studentService,
    protected RecommendationService $recommendationService,
  ) {
    // 
  }

  protected function students()
  {
    $students = $this->studentService->getWhere(
      orderBy: 'name',
      orderByType: 'asc',
    )->get();

    return $students;
  }

  protected function studentDetail($studentId)
  {
    $student = $this->studentService->findOrFail($studentId);

    // Hitung total SKS yang sudah ditempuh
    $recommendedSubjects = $this->recommendationService->getWhere(
      wheres: [
        'student_id' => $student->id
      ]
    )->pluck('subject_id');

    // Ambil ID mata kuliah yang dinilai dengan nilai bukan 'E'
    $passedSubjects = Grade::where('student_id', $student->id)
      ->whereIn('subject_id', $recommendedSubjects)
      ->where('grade', '!=', GradeType::E->value)
      ->pluck('subject_id');

    // Hitung total SKS dari mata kuliah yang lulus
    $totalCompletedCourseCredit = Subject::whereIn('id', $passedSubjects)->sum('course_credit');
    $totalCourseCredit = $student->major->total_course_credit;

    $details = [
      'total_course_credit' => $totalCourseCredit,
      'total_course_credit_done' => $totalCompletedCourseCredit,
      'total_course_credit_remainder' => $totalCourseCredit - $totalCompletedCourseCredit,
    ];

    return $details;
  }

  protected function courses($studentId)
  {
    // Student Data
    $student = $this->studentService->findOrFail($studentId);
    $majorId = $student->major->id;

    // Ambil ID mata kuliah yang sudah direkomendasikan untuk mahasiswa ini
    $recommendedSubjects = $this->recommendationService->getWhere(
      wheres: [
        'student_id' => $student->id,
      ],
      columns: 'subject_id',
    )->get();

    $recommendedSubjectIds = $recommendedSubjects->pluck('subject_id')->toArray();

    // Ambil ID mata kuliah dengan nilai 'E' yang sudah direkomendasikan
    $subjectIdsWithEGrade = Grade::where('student_id', $student->id)
      ->whereIn('subject_id', $recommendedSubjectIds)
      ->where('grade', GradeType::E->value)
      ->pluck('subject_id')
      ->toArray();

    // Query untuk mengambil semua mata kuliah berdasarkan majorId
    $subjects = Subject::whereHas('majors', function ($query) use ($majorId) {
      $query->where('majors.id', $majorId);
    })->with(['majors' => function ($query) use ($majorId) {
      $query->where('majors.id', $majorId);
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

    return $formattedSubjectsBySemester;
  }

  /**
   * Display a listing of the resource.
   */
  public function index(StudentDataTable $dataTable)
  {
    $students = $this->students();
    return $dataTable->render('evaluations.recommendations.index', compact('students'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(Student $student)
  {
    $data = $this->studentDetail($student->id);
    return view('evaluations.recommendations.create', compact('student', 'data'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    dd($request->all());

    $this->recommendationService->handleStoreData($request);
    return redirect(route('recommendations.index'))->withSuccess(trans('session.create'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Recommendation $recommendation)
  {
    return $this->recommendationService->handleDestroyData($recommendation->id);
  }

  public function export(RecommendationExportRequest $request)
  {
    return $this->recommendationService->handleExportData($request);
  }
}
