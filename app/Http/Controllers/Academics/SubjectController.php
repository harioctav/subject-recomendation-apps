<?php

namespace App\Http\Controllers\Academics;

use App\Models\Subject;
use App\Traits\ChacesData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Major\MajorService;
use App\Helpers\Enums\SubjectNoteType;
use App\Helpers\Enums\StatusSubjectType;
use App\Services\Subject\SubjectService;
use App\Http\Requests\Imports\ImportRequest;
use App\DataTables\Academics\SubjectDataTable;
use App\Helpers\Helper;
use App\Http\Requests\Academics\SubjectRequest;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class SubjectController extends Controller
{
  use ChacesData;

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
  public function index(SubjectDataTable $dataTable)
  {
    return $dataTable->render('academics.subjects.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $majors = $this->majorService->getWhere(
      orderBy: 'name',
      orderByType: 'asc',
    )->get();

    $notes = $this->cacheData('subject_notes', function () {
      return SubjectNoteType::toArray();
    });

    $status = $this->cacheData('subject_status', function () {
      return StatusSubjectType::toArray();
    });

    return view('academics.subjects.create', compact('majors', 'status', 'notes'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(SubjectRequest $request)
  {
    $this->subjectService->handleStoreData($request);
    return redirect(route('subjects.index'))->withSuccess(trans('session.create'));
  }

  /**
   * Display the specified resource.
   */
  public function show(Subject $subject)
  {
    return view('academics.subjects.show', compact('subject'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Subject $subject)
  {
    $majors = $this->majorService->getWhere(
      orderBy: 'name',
      orderByType: 'asc',
    )->get();

    $notes = $this->cacheData('subject_notes', function () {
      return SubjectNoteType::toArray();
    });

    $status = $this->cacheData('subject_status', function () {
      return StatusSubjectType::toArray();
    });

    return view('academics.subjects.edit', compact('majors', 'status', 'notes', 'subject'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(SubjectRequest $request, Subject $subject)
  {
    $this->subjectService->handleUpdateData($request, $subject->id);
    return redirect(route('subjects.index'))->withSuccess(trans('session.update'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Subject $subject)
  {
    try {
      // Activity Log
      Helper::log(
        trans('activity.subjects.destroy', ['subject' => $subject->name]),
        me()->id,
        'subject_activity_destroy',
        [
          'data' => $subject
        ]
      );

      $this->subjectService->delete($subject->id);
      return response()->json([
        'message' => trans('session.delete'),
      ]);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Import Data to Database.
   *
   */
  public function Import(ImportRequest $request)
  {
    $this->subjectService->handleImportData($request);
    return redirect(route('subjects.index'))->withSuccess(trans('session.create'));
  }
}
