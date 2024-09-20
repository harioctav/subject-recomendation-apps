<?php

namespace App\Http\Controllers\Evaluations;

use App\DataTables\Scopes\GradeFilter;
use App\Http\Requests\Imports\ImportRequest;
use App\Models\Grade;
use App\Http\Controllers\Controller;
use App\Services\Grade\GradeService;
use App\Services\Major\MajorService;
use App\Services\Student\StudentService;
use App\DataTables\Evaluations\GradeDataTable;
use App\DataTables\Evaluations\StudentDataTable;
use App\Helpers\Enums\GradeType;
use App\Http\Requests\Evaluations\GradeRequest;
use App\Http\Requests\Evaluations\RecommendationExportRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected GradeService $gradeService,
    protected MajorService $majorService,
    protected StudentService $studentService,
  ) {
    // 
  }

  /**
   * Display a listing of the resource.
   */
  public function index(StudentDataTable $dataTable, Request $request)
  {
    return $dataTable->render('evaluations.grades.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(Student $student)
  {
    $grades = GradeType::toArray();
    $recommendations = $this->studentService->getStudentDataWithRecommendations($student);

    return view('evaluations.grades.create', compact('student', 'grades', 'recommendations'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(GradeRequest $request, Student $student)
  {
    $this->gradeService->handleStoreData($request);
    return redirect(route('grades.show', $student))->withSuccess(trans('session.create'));
  }

  /**
   * Display the grades for the specified student.
   * 
   */
  public function show(Student $student)
  {
    $dataTable = new GradeDataTable($student->id);
    $detail = $this->studentService->getStudentAcademicInfo($student->id);

    return $dataTable->render('evaluations.grades.show', compact('student', 'detail'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Grade $grade, Student $student)
  {
    $grades = GradeType::toArray();
    return view('evaluations.grades.edit', compact('grade', 'grades'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(GradeRequest $request, Grade $grade,  Student $student)
  {
    $this->gradeService->handleUpdateData($request, $grade->id);
    return redirect(route('grades.show', $student))->withSuccess(trans('session.update'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Grade $grade)
  {
    $this->gradeService->handleDestroyData($grade->id);
    return response()->json([
      'success' => true,
      'message' => trans('session.delete'),
    ]);
  }

  public function export(Student $student)
  {
    return $this->gradeService->handleExportData($student);
  }

  public function import(Student $student, ImportRequest $request)
  {
    return $this->gradeService->handleImportData($student, $request);
  }
}
