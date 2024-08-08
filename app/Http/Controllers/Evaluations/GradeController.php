<?php

namespace App\Http\Controllers\Evaluations;

use App\DataTables\Scopes\GradeFilter;
use App\Models\Grade;
use App\Http\Controllers\Controller;
use App\Services\Grade\GradeService;
use App\Services\Major\MajorService;
use Illuminate\Support\Facades\Cache;
use App\Services\Student\StudentService;
use App\DataTables\Evaluations\GradeDataTable;
use App\Helpers\Enums\GradeType;
use App\Http\Requests\Evaluations\GradeRequest;
use App\Http\Requests\Evaluations\RecommendationExportRequest;
use Illuminate\Http\Request;

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

  protected function majors()
  {
    return $this->majorService->getWhere(
      orderBy: 'name',
      orderByType: 'asc',
    )->get();
  }

  protected function students()
  {
    $students = $this->studentService->getWhere(
      orderBy: 'name',
      orderByType: 'asc',
    )->get();

    return $students;
  }

  /**
   * Display a listing of the resource.
   */
  public function index(GradeDataTable $dataTable, Request $request)
  {
    $students = $this->students();
    return $dataTable
      ->addScope(new GradeFilter($request))
      ->render('evaluations.grades.index', compact('students'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $students = $this->students();
    $grades = GradeType::toArray();

    return view('evaluations.grades.create', compact('students', 'grades'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(GradeRequest $request)
  {
    $this->gradeService->handleStoreData($request);
    return redirect(route('grades.index'))->withSuccess(trans('session.create'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Grade $grade)
  {
    $grades = GradeType::toArray();
    return view('evaluations.grades.edit', compact('grade', 'grades'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(GradeRequest $request, Grade $grade)
  {
    $this->gradeService->handleUpdateData($request, $grade->id);
    return redirect(route('grades.index'))->withSuccess(trans('session.update'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Grade $grade)
  {
    $this->gradeService->delete($grade->id);
    return response()->json([
      'message' => trans('session.delete'),
    ]);
  }

  public function export(RecommendationExportRequest $request)
  {
    return $this->gradeService->handleExportData($request);
  }
}
