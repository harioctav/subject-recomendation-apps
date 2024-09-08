<?php

namespace App\Http\Controllers\Evaluations;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Repositories\Subject\SubjectRepository;
use Illuminate\Http\Request;
use App\Models\Recommendation;
use App\Helpers\Enums\GradeType;
use App\Http\Controllers\Controller;
use App\Services\Student\StudentService;
use App\DataTables\Evaluations\StudentDataTable;
use App\Services\Recommendation\RecommendationService;
use App\DataTables\Evaluations\RecommendationDataTable;
use App\Helpers\Helper;
use App\Http\Requests\Evaluations\RecommendationRequest;
use App\Http\Requests\Evaluations\RecommendationExportRequest;
use App\Services\Grade\GradeService;

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
    protected GradeService $gradeService,
    protected SubjectRepository $subjectRepository
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

    $gpa = Helper::calculateGPA($student->id);

    $hasGradeE = Grade::where('student_id', $student->id)
      ->where('grade', GradeType::E->value)
      ->exists();

    $details = [
      'total_course_credit' => $totalCourseCredit,
      'total_course_credit_done' => $totalCompletedCourseCredit,
      'total_course_credit_remainder' => $totalCourseCredit - $totalCompletedCourseCredit,
      'gpa' => $gpa,
      'has_grade_e' => $hasGradeE
    ];

    return $details;
  }
  /**
   * Display a listing of the resource.
   */
  public function index(StudentDataTable $dataTable)
  {
    return $dataTable->render('evaluations.recommendations.index');
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
  public function store(RecommendationRequest $request, Student $student)
  {
    $this->recommendationService->handleStoreData($request);
    return redirect(route('recommendations.show', $student))->withSuccess(trans('session.create'));
  }

  public function show(Student $student)
  {
    $data = $this->studentDetail($student->id);
    $dataTable = new RecommendationDataTable($student->id);

    return $dataTable->render('evaluations.recommendations.show', compact('student', 'data', 'dataTable'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Recommendation $recommendation)
  {
    return $this->recommendationService->handleDestroyData($recommendation->id);
  }

  public function export(Student $student)
  {
    return $this->recommendationService->handleExportData($student);
  }
}
