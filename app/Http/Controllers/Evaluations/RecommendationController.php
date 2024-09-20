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
    $detail = $this->studentService->getStudentAcademicInfo($student->id);

    return view('evaluations.recommendations.create', compact('student', 'detail'));
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
    $detail = $this->studentService->getStudentAcademicInfo($student->id);
    $dataTable = new RecommendationDataTable($student->id);

    return $dataTable->render('evaluations.recommendations.show', compact('student', 'detail', 'dataTable'));
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
