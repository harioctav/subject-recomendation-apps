<?php

namespace App\Http\Controllers\Academics;

use App\Models\Student;
use App\Traits\ChacesData;
use Illuminate\Http\Request;
use App\Helpers\Enums\GenderType;
use App\Helpers\Enums\ReligionType;
use App\Http\Controllers\Controller;
use App\Services\Major\MajorService;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Enums\StudentStatusType;
use App\Services\Student\StudentService;
use App\DataTables\Scopes\UserStatusFilter;
use App\DataTables\Academics\StudentDataTable;
use App\Http\Requests\Academics\StudentRequest;
use App\Services\Locations\Province\ProvinceService;

class StudentController extends Controller
{
  use ChacesData;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected MajorService $majorService,
    protected StudentService $studentService,
    protected ProvinceService $provinceService,
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

  protected function provinces()
  {
    return $this->provinceService->getWhere(
      orderBy: 'id',
      orderByType: 'ASC'
    )->get();
  }

  /**
   * Display a listing of the resource.
   */
  public function index(StudentDataTable $dataTable, Request $request)
  {
    $status = StudentStatusType::toArray();
    return $dataTable->addScope(new UserStatusFilter($request))->render("academics.students.index", compact('status'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $majors = $this->majors();
    $provinces = $this->provinces();

    $genders = $this->cacheData('genders', fn () => GenderType::toArray());
    $religions = $this->cacheData('religions', fn () => ReligionType::toArray());
    $status = $this->cacheData('status', fn () => StudentStatusType::toArray());

    return view('academics.students.create', compact('majors', 'genders', 'religions', 'provinces', 'status'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StudentRequest $request)
  {
    $this->studentService->handleStoreData($request);
    return redirect(route('students.index'))->withSuccess(trans('session.create'));
  }

  /**
   * Display the specified resource.
   */
  public function show(Student $student)
  {
    return view('academics.students.show', compact('student'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Student $student)
  {
    $majors = $this->majors();
    $provinces = $this->provinces();

    $genders = $this->cacheData('genders', fn () => GenderType::toArray());
    $religions = $this->cacheData('religions', fn () => ReligionType::toArray());
    $status = $this->cacheData('status', fn () => StudentStatusType::toArray());

    return view('academics.students.edit', compact('majors', 'genders', 'religions', 'student', 'provinces', 'status'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(StudentRequest $request, Student $student)
  {
    $this->studentService->handleUpdateData($request, $student->id);
    return redirect(route('students.index'))->withSuccess(trans('session.update'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Student $student)
  {
    $this->studentService->handleDeleteData($student->id);
    return response()->json([
      'message' => trans('session.delete'),
    ]);
  }
}
