<?php

namespace App\Http\Controllers\Academics;

use App\Models\Major;
use App\Helpers\Enums\DegreeType;
use App\Http\Controllers\Controller;
use App\Services\Major\MajorService;
use App\Services\Subject\SubjectService;
use App\DataTables\Academics\MajorDataTable;
use App\Http\Requests\Academics\MajorRequest;
use App\DataTables\Academics\MajorSubjectDataTable;

class MajorController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected MajorService $majorService,
    protected SubjectService $subjectService,
  ) {
    // 
  }

  /**
   * Display a listing of the resource.
   */
  public function index(MajorDataTable $dataTable)
  {
    return $dataTable->render('academics.majors.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $degrees = DegreeType::toArray();
    return view('academics.majors.create', compact('degrees'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(MajorRequest $request)
  {
    $this->majorService->handleStoreData($request);
    return redirect(route('majors.index'))->withSuccess(trans('session.create'));
  }

  /**
   * Display the specified resource.
   */
  public function show(Major $major)
  {
    $dataTable = new MajorSubjectDataTable($major->id);
    return $dataTable->render('academics.majors.show', compact('major', 'dataTable'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Major $major)
  {
    $degrees = DegreeType::toArray();
    return view('academics.majors.edit', compact('degrees', 'major'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(MajorRequest $request, Major $major)
  {
    $this->majorService->handleUpdateData($request, $major->id);
    return redirect(route('majors.index'))->withSuccess(trans('session.update'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Major $major)
  {
    return $this->majorService->handleDestroyData($major->id);
  }
}
