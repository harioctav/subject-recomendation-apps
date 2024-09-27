<?php

namespace App\Http\Controllers\Academics;

use App\Models\Student;
use App\Traits\ChacesData;
use App\Helpers\Enums\GenderType;
use App\Helpers\Enums\ReligionType;
use App\Http\Controllers\Controller;
use App\Services\Major\MajorService;
use App\Helpers\Enums\StudentStatusType;
use App\Services\Student\StudentService;
use App\DataTables\Academics\StudentDataTable;
use App\Http\Requests\Academics\StudentRequest;
use App\Http\Requests\Academics\Students\NimRequest;
use App\Http\Requests\Imports\ImportRequest;
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
  public function index(StudentDataTable $dataTable)
  {
    $status = StudentStatusType::toArray();
    return $dataTable->render('academics.students.index', compact('status'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $majors = $this->majors();
    $provinces = $this->provinces();

    $genders = $this->cacheData('genders', fn() => GenderType::toArray());
    $religions = $this->cacheData('religions', fn() => ReligionType::toArray());
    $status = $this->cacheData('status', fn() => StudentStatusType::toArray());

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
    $student->major;
    $student->grades;
    optional($student->province);
    $student->avatar = $student->getAvatar();

    return response()->json([
      'student' => $student
    ], 201);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Student $student)
  {
    $majors = $this->majors();
    $provinces = $this->provinces();

    $genders = $this->cacheData('genders', fn() => GenderType::toArray());
    $religions = $this->cacheData('religions', fn() => ReligionType::toArray());
    $status = $this->cacheData('status', fn() => StudentStatusType::toArray());

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

  public function restore(Student $student)
  {
    $this->studentService->handleRestoreData($student->id);
    return response()->json([
      'message' => trans('session.restore'),
    ]);
  }

  public function delete(Student $student)
  {
    $this->studentService->handleForceDeleteData($student->id);
    return response()->json([
      'message' => trans('session.force-delete'),
    ]);
  }

  public function data(NimRequest $request)
  {
    return $this->studentService->getStudentAllData($request);
  }

  public function import(ImportRequest $request)
  {
    return $this->studentService->handleImportData($request);
  }
}
