<?php

namespace App\Http\Controllers\Evaluations;

use App\Models\Grade;
use Illuminate\Http\Request;
use App\Models\Recommendation;
use App\Http\Controllers\Controller;
use App\Services\Grade\GradeService;
use App\Services\Major\MajorService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Services\Student\StudentService;
use App\Services\Subject\SubjectService;
use App\DataTables\Evaluations\GradeDataTable;
use App\Http\Requests\Evaluations\GradeRequest;
use App\Http\Requests\Evaluations\Grades\FormSatuRequest;
use App\Models\MajorSubject;

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
    protected SubjectService $subjectService
  ) {
    // 
  }

  protected function majors()
  {
    return Cache::remember("majors", 60 * 60, function () {
      return $this->majorService->getWhere(
        orderBy: 'name',
        orderByType: 'asc',
      )->get();
    });
  }

  /**
   * Display a listing of the resource.
   */
  public function index(GradeDataTable $dataTable)
  {
    return $dataTable->render('evaluations.grades.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $majors = $this->majors();
    return view('evaluations.grades.create', compact('majors'));
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
    return view('evaluations.grades.edit', compact('grade'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(GradeRequest $request, Grade $grade)
  {
    $this->gradeService->update($grade->id, $request->validated());
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
}
