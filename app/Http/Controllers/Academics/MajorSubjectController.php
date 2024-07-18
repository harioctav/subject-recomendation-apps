<?php

namespace App\Http\Controllers\Academics;

use App\Models\Major;
use App\Models\Subject;
use App\Traits\ChacesData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Major\MajorService;
use App\Helpers\Enums\SemesterLevelType;
use App\Services\Subject\SubjectService;
use App\Http\Requests\Academics\MajorSubjectRequest;

class MajorSubjectController extends Controller
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
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(Major $major)
  {
    $subjects = $this->subjectService->getQuery()->get();

    $semesters = $this->cacheData('subject_semesters', function () {
      return SemesterLevelType::toArray();
    });

    return view('academics.major_subjects.create', compact('subjects', 'major', 'semesters'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(MajorSubjectRequest $request, Major $major)
  {
    $this->majorService->handleStoreSubjectToMajorData($request, $major);
    return redirect(route('majors.show', $major))->withSuccess(trans('session.create'));
  }

  /**
   * Display the specified resource.
   */
  public function show(Major $major)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Major $major)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Major $major)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Major $major, Subject $subject)
  {
    return $this->majorService->handleDestroySubjectToMajorData($major, $subject);
  }
}
